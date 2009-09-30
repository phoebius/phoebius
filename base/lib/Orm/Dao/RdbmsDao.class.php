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

	function __construct(
			DB $db,
			IOrmEntityMapper $map,
			ILogicallySchematic $logicalSchema,
			IPhysicallySchematic $physicalSchema
		)
	{
		$this->db = $db;
		$this->map = $map;
		$this->logicalSchema = $logicalSchema;
		$this->physicalSchema = $physicalSchema;

		if (($this->identitifier = $logicalSchema->getIdentifier())) {
			$this->identityMap = new OrmIdentityMap($logicalSchema);
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
					$this->getSelectQuery()->setCondition(
						$this->getEqExpression(
							$this->identitifier,
							$this->identitifier->getType()->makeRawValue($id)
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

			$fetchExpression = Expression::orChain();
			foreach ($toFetchIds as $id) {
				$fetchExpression->add(
					$this->getEqExpression(
						$this->identitifier,
						$this->identitifier->getType()->makeRawValue($id)
					)
				);
			}

			$newFetched = $this->getListByCondition($fetchExpression);

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
	function getByCondition(IDalExpression $condition)
	{
		return $this->getByQuery(
			$this->getSelectQuery()->setCondition($condition)
		);
	}

	/**
	 * @return array of {@link OrmEntity}
	 */
	function getListByCondition(IDalExpression $condition)
	{
		return $this->getListByQuery(
			$this->getSelectQuery()->setCondition($condition)
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
		$ormQuery = $this->physicalSchema->getOrmQuery();
		foreach ($this->logicalSchema->getProperties() as $propertyName => $property) {
			$rawValueSet[$propertyName] = $ormQuery->makeRawValue($property, $dbValues);
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
				->setCondition(
					$this->getEqExpression(
						$this->identitifier,
						$this->identitifier->getType()->makeRawValue($id)
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

		$expression = Expression::orChain();

		foreach ($ids as $id) {

			$this->identityMap->dropFromIdentityMap($id);

			$expression->add(
				$this->getEqExpression(
					$this->identitifier,
					$this->identitifier->getType()->makeRawValue($id)
				)
			);
		}

		$affected = $this->sendQuery(
			DeleteQuery::create($this->physicalSchema->getDBTableName())
				->setCondition(
					$expression
				)
		);

		return $affected;
	}

	/**
	 * @return integer
	 */
	function dropByCondition(IDalExpression $condition)
	{
		$affected = $this->sendQuery(
			DeleteQuery::create($this->physicalSchema->getDBTableName())
				->setCondition($condition)
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
	 * @return OrmEntity
	 */
	function save(OrmEntity $entity)
	{
		$hasId = false;
		$fetched = true;

		if ($this->identitifier) {
			$id = $entity->_getId();

			// INSERT
			if (empty($id)) {
				$fetched = false;
				$sequencedColumns = $this->physicalSchema->getDbColumns($this->identitifier);
				foreach ($sequencedColumns as $k => &$v) {
					$v = $this->getDB()->preGenerate($this->physicalSchema->getDBTableName(), $k);
					$fetched =
						$fetched
							? true
							: !empty($v);
				}

				if ($fetched) {
					$id = $this->identitifier->getType()->makeValue(
						$this->physicalSchema->getOrmQuery()->makeRawValue(
							$this->identitifier, $sequencedColumns
						),
						$this->getFetchStrategy()
					);

					$entity->fetch()->_setId($id);
				}
			}
			// UPDATE, or INSERT
			else {
				if ($this->identityMap->isInIdentityMap($id)) {
					$hasId = true;
				}

				$entity->_setId(null);
				$entity->fetch();
				$entity->_setId($id);
			}
		}


		$fvc = new SqlFieldValueCollection();
		$propertyMap = $this->logicalSchema->getProperties();
		$identifierRawValue = null;
		foreach ($this->map->getRawValues($entity) as $propertyName => $rawValue) {
			$property = $propertyMap[$propertyName];
			foreach ($this->getDBValues($property, $rawValue) as $column => $sqlValue) {
				$fvc->add(
					$column,
					$sqlValue
				);
			}

			if ($this->identitifier === $property) {
				$identifierRawValue = $rawValue;
			}
		}

		if ($this->identitifier && $hasId) {
			$affected = $this->sendQuery(
				UpdateQuery::create($this->physicalSchema->getDBTableName())
					->setFieldValueCollection(
						$fvc
					)
					->setCondition(
						$this->getEqExpression(
							$this->identitifier,
							$identifierRawValue
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
				InsertQuery::create($this->physicalSchema->getDBTableName())
					->setFieldValueCollection($fvc)
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
			foreach ($sequencedColumns as $k => &$v) {
				$v = $this->getDB()->getGeneratedId($this->physicalSchema->getDBTableName(), $k);
			}

			$id = $this->identitifier->getType()->makeValue(
				$this->physicalSchema->getOrmQuery()->makeRawValue(
					$this->identitifier, $sequencedColumns
				),
				$this->getFetchStrategy()
			);

			$entity->_setId($id);
		}

		if ($this->identityMap) {
			$this->identityMap->addToIdentityMap($entity);
		}

		return $entity;
	}

	/**
	 * @return array
	 */
	private function getDBValues(OrmProperty $property, $rawValue)
	{
		return $this->physicalSchema->getOrmQuery()->makeColumnValue(
			$property,
			$rawValue
		);
	}

	/**
	 * @return ExpressionChain
	 */
	private function getEqExpression(OrmProperty $property, array $rawValue)
	{
		$expression = Expression::andChain();
		$table = $this->physicalSchema->getDBTableName();
		foreach ($this->getDBValues($property, $rawValue) as $column => $sqlValue) {
			$expression->add(
				Expression::eq(
					new SqlColumn(
						$column,
						$table
					),
					$sqlValue
				)
			);
		}

		return $expression;
	}

	/**
	 * @return SelectQuery
	 */
	function getSelectQuery($aliasPrefix = null)
	{
		$table = $this->physicalSchema->getDBTableName();

		$selectQuery =
			SelectQuery::create()
				->from($table);

		foreach ($this->physicalSchema->getDbColumns() as $column => $type) {
			$selectQuery->get(
				$column,
				($aliasPrefix ? $aliasPrefix . $column : null),
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