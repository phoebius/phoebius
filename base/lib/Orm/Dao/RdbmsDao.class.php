<?php
/* ***********************************************************************************************
 *
 * Phoebius Framework
 *
 * **********************************************************************************************
 *
 * Copyright notice
 *
 ************************************************************************************************/

/**
 * @ingroup Dao
 */
class RdbmsDao implements IOrmEntityAccessor
{
	/**
	 * @var FetchStrategy
	 */
	private $fetchStrategy;

	/**
	 * @var DB
	 */
	private $db;

	/**
	 * @var ILogicallySchematic
	 */
	private $logicalSchema;

	/**
	 * @var IPhysicallySchematic
	 */
	private $physicalSchema;

	/**
	 * @var IOrmEntityMapper
	 */
	private $map;

	/**
	 * null if orm entity is identifierless
	 * @var OrmIdentityMap|null
	 */
	private $identityMap;

	/**
	 * null if orm entity is identifierless
	 * @var OrmProperty|null
	 */
	private $identitifier;

	/**
	 * @var IQueried
	 */
	private $entity;

	function __construct(
			DB $db,
			IQueried $entity
		)
	{
		$this->db = $db;
		$this->entity = $entity;
		$this->map = $entity->getMap();
		$this->logicalSchema = $entity->getLogicalSchema();
		$this->physicalSchema = $entity->getPhysicalSchema();

		if (($this->identitifier = $this->logicalSchema->getIdentifier())) {
			$this->identityMap = new OrmIdentityMap($this->logicalSchema);
		}
	}

	/**
	 * @return DbDao
	 */
	function setFetchStrategy(FetchStrategy $fs)
	{
		$this->fetchStrategy = $fs;

		return $this;
	}

	/**
	 * @return FetchStrategy
	 */
	function getFetchStrategy()
	{
		if (!$this->fetchStrategy) {
			$this->fetchStrategy = FetchStrategy::cascade();
		}

		return $this->fetchStrategy;
	}

	/**
	 * @throws OrmEntityNotFoundException
	 * @return OrmEntity
	 */
	function getById($id)
	{
		Assert::isNotEmpty($this->identitifier, 'identifierless orm entity');

		$entity = $this->identityMap->getLazyFromIdentityMap($id);

		// Avoid replace this code with $entity->fetch() because IdentifiableOrmEntity::fetch()
		// calls RdbmsDao::getById() to be filled with data
		if (!OrmUtils::isFetchedEntity($entity)) {
			try {
				$row = $this->getDB()->getRow(
					$this->getSelectQuery()->setExpression(
						EntityQuery::create($this->entity)
							->where(
								$this->identitifier,
								$id
							)
					)
				);
			}
			catch (RowNotFoundException $e) {
				throw new OrmEntityNotFoundException($this->logicalSchema);
			}

			$this->buildEntity(
				$entity,
				$row
			);
		}

		return $entity;
	}

	/**
	 * @return OrmEntity
	 */
	function getLazyById($id)
	{
		Assert::isNotEmpty($this->identitifier, 'identifierless orm entity');

		return $this->identityMap->getLazyFromIdentityMap($id);
	}

	/**
	 * Similar to IOrmEntityAccessor::getById() but for multiple entities at once. If one or more
	 * entities of the set not found, they won't be presented in the result set of entities
	 * @return array of {@link OrmEntity}
	 */
	function getByIds(array $ids)
	{
		Assert::isNotEmpty($this->identitifier, 'identifierless orm entity');

		$fetched = array();
		$toFetch = array();
		$toFetchIds = array();

		foreach ($ids as $id) {
			$entity = $this->identityMap->getLazyFromIdentityMap($id);

			if (OrmUtils::isFetchedEntity($entity)) {
				$fetched[] = $entity;
			}
			else {
				$toFetch[spl_object_hash($entity)] = $entity;
				$toFetchIds[] = $id;
			}
		}

		if (!empty($toFetch)) {
			$fetchExpression = new OrmQuery($this->entity, ExpressionChainPredicate::conditionOr());

			$fetchExpression = Expression::orChain();
			foreach ($toFetchIds as $id) {
				$fetchExpression->add(
					$this->identifier,
					$id
				);
			}

			$newFetched = $this->getCustomBy($fetchExpression);

			foreach ($newFetched as $entity) {
				unset ($toFetch[spl_object_hash($entity)]);
				$fetched[] = $entity;
			}

			// if there were some ID collisions - we should remove them from identityMap
			if (!empty($toFetch)) {
				foreach ($toFetch as $entity) {
					$this->identityMap->dropFromIdentityMap($entity->_getId());
				}
			}
		}

		return $fetched;
	}

	/**
	 * @return array
	 */
	function getList()
	{
		return $this->getListByQuery(
			$this->getSelectQuery()
		);
	}

	/**
	 * @throws OrmEntityNotFoundException
	 * @return OrmEntity
	 */
	function getBy(IDalExpression $condition)
	{
		return $this->getByQuery(
			$this->getSelectQuery()->setExpression($condition)
		);
	}

	/**
	 * @return array of {@link OrmEntity}
	 */
	function getCustomBy(IDalExpression $condition)
	{
		return $this->getListByQuery(
			$this->getSelectQuery()->setExpression($condition)
		);
	}

	/**
	 * @throws OrmEntityNotFoundException
	 * @return OrmEntity
	 */
	function getByQuery(ISqlSelectQuery $query)
	{
		return $this->buildEntity(
			$this->logicalSchema->getNewEntity(),
			$this->getDbValueSetByQuery($query)
		);
	}

	private function getDbValueSetByQuery(ISqlSelectQuery $query)
	{
		$query->setLimit(1);

		try {
			return $this->getDB()->getRow($query);
		}
		catch (RowNotFoundException $e) {
			throw new OrmEntityNotFoundException($this->logicalSchema);
		}
	}

	/**
	 * @return OrmEntity
	 */
	private function buildEntity(OrmEntity $entity, array $dbValues)
	{
		$rawValueSet = array();
		foreach ($this->logicalSchema->getProperties() as $propertyName => $property) {
			foreach ($property->getDbColumns() as $columnName => $dbType) {
				$rawValueSet[$propertyName][] = $dbValues[$columnName];
			}
		}

		if ($this->identitifier) {
			$entity->fetch();
		}

		$this->map->setRawValues($entity, $rawValueSet, $this->getFetchStrategy());

		if ($this->identityMap) {
			$this->identityMap->addToIdentityMap($entity);
		}

		return $entity;
	}

	/**
	 * @return array
	 */
	function getListByQuery(ISqlSelectQuery $query)
	{
		$rows = $this->getDB()->getRows($query);

		$entities = array();

		if (!empty($rows)) {

			$batchId = $this->map->beginBatchFetchingMode();

			foreach ($rows as $row) {
				$entities[] = $this->buildEntity($this->logicalSchema->getNewEntity(), $row);
			}

			 $this->map->commitBatchFetchingMode($batchId);
		}

		return $entities;
	}

	/**
	 * @return array
	 */
	function getCustomRowByQuery(ISqlSelectQuery $query)
	{
		return $this->getDB()->getRow($query);
	}

	/**
	 * @return array
	 */
	function getCustomRowsByQuery(ISqlSelectQuery $query)
	{
		return $this->getDB()->getRows($query);
	}

	/**
	 * Returns the number of affected rows
	 * @return integer
	 */
	function sendQuery(ISqlQuery $query)
	{
		$result = $this->getDB()->sendQuery($query, false);
		return $this->getDB()->getAffectedRowsNumber($result);
	}

	/**
	 * @return boolean
	 */
	function dropById($id)
	{
		Assert::isNotEmpty($this->identitifier, 'identifierless orm entity');

		$affected = $this->sendQuery(
			DeleteQuery::create($this->physicalSchema->getDBTableName())
				->setExpression(
					OrmQuery::create($this->entity)
						->where(
							$this->identifier,
							$id
						)
				)
		);

		$this->identityMap->dropFromIdentityMap($id);

		Assert::isTrue(($affected == 0) || ($affected == 1));

		return $affected == 1;
	}

	/**
	 * @return integer
	 */
	function dropByIds(array $ids)
	{
		Assert::isNotEmpty($this->identitifier, 'identifierless orm entity');

		$expression = OrmQuery::create($this->entity, ExpressionChainPredicate::conditionOr());

		foreach ($ids as $id) {

			$this->identityMap->dropFromIdentityMap($id);

			$expression->add(
				$this->identitifier,
				$id
			);
		}

		$affected = $this->sendQuery(
			DeleteQuery::create($this->physicalSchema->getDBTableName())
				->setExpression(
					$expression
				)
		);

		return $affected;
	}

	/**
	 * @return integer
	 */
	function dropBy(IDalExpression $condition)
	{
		$affected = $this->sendQuery(
			DeleteQuery::create($this->physicalSchema->getDBTableName())
				->setExpression($condition)
		);

		if ($this->identityMap && $affected > 0) {
			$this->identityMap->dropIdentityMap();
		}

		return $affected;
	}

	/**
	 * @return boolean
	 */
	function drop(IdentifiableOrmEntity $entity)
	{
		Assert::isNotEmpty($this->identitifier, 'identifierless orm entity');

		return $this->dropById($entity->_getId());
	}

	/**
	 * @return SqlFieldValueCollection
	 */
	private function getFieldValueCollection(OrmEntity $entity)
	{
		$fvc = new SqlFieldValueCollection();
		foreach ($this->map->getRawValues($entity) as $propertyName => $rawValue) {
			$fvc->addCollection(
				array_combine(
					array_keys($this->logicalSchema->getProperty($propertyName)->getDBColumns()),
					$rawValue
				)
			);
		}

		return $fvc;
	}

	/**
	 * @return EntityQuery
	 */
	function getExpression($property, IExpression $expression)
	{
		return EntityQuery::create($this->entity)
			->where($property, $expression);
	}

	/**
	 * @return OrmEntity
	 */
	function save(OrmEntity $entity)
	{
		$fetched = true;

		if ($this->identitifier) {
			$id = $entity->_getId();
			$idOrmPropertyType = $this->identitifier->getType();

			// INSERT
			if (empty($id)) {
				if ($idOrmPropertyType instanceof IGenerated) {
					$fetched = false;
					$id = $idOrmPropertyType->preGenerate(
						$this->getDB(),
						$this->physicalSchema->getDBTableName(),
						$this->identifier
					);
				}
			}

			$entity->_setId(null);
			$entity->fetch();
			$entity->_setId($id);
		}

		$fvc = $this->getFieldValueCollection($entity);

		if ($this->identitifier && $fetched) {
			$affected = $this->sendQuery(
				new UpdateQuery(
					$this->physicalSchema->getDBTableName(),
					$fvc,
					$this->getExpression(
						$this->identifier,
						Expression::eq($entity->_getId())
					)
				)
			);

			Assert::isTrue(($affected == 0) || ($affected == 1));

			if ($affected == 1) {
				return $entity;
			}
			else { // for mysql
				$trySafeInsert = true;
			}
		}

		try {
			$affected = $this->sendQuery(
				new InsertQuery(
					$this->physicalSchema->getDBTableName(),
					$fvc
				)
			);
		}
		catch (UniqueViolationException $e) {
			if (isset($trySafeInsert)) {
				return $entity;
			}

			throw $e;
		}

		Assert::isTrue($affected == 1);

		if ($this->identitifier && !$fetched) {
			$id = $idOrmPropertyType->getGeneratedId(
				$this->getDB(),
				$this->physicalSchema->getDBTableName(),
				$this->identifier
			);

			$entity->_setId($id);
		}

		if ($this->identityMap) {
			$this->identityMap->addToIdentityMap($entity);
		}

		return $entity;
	}

	/**
	 * @return SelectQuery
	 */
	function getSelectQuery()
	{
		$table = $this->physicalSchema->getDBTableName();

		$selectQuery =
			SelectQuery::create()
				->from($table);

		foreach ($this->physicalSchema->getDbColumns() as $column => $type) {
			$selectQuery->get(
				$column,
				null,
				$table
			);
		}

		return $selectQuery;
	}

	/**
	 * @return DB
	 */
	private function getDB()
	{
		return $this->db->connect(false);
	}
}

?>