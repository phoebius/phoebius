<?php
/* ***********************************************************************************************
 *
 * Phoebius Framework
 *
 * **********************************************************************************************
 *
 * Copyright (c) 2009 phoebius.org
 *
 * This program is free software; you can redistribute it and/or modify it under the terms
 * of the GNU Lesser General Public License as published by the Free Software Foundation;
 * either version 3 of the License, or (at your option) any later version.
 *
 * You should have received a copy of the GNU Lesser General Public License along with
 * this program; if not, see <http://www.gnu.org/licenses/>.
 *
 ************************************************************************************************/

/**
 * @ingroup Orm_Dao
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
	private $identifier;

	/**
	 * @var IQueryable
	 */
	private $entity;

	function __construct(
			DB $db,
			IQueryable $entity
		)
	{
		$this->db = $db;
		$this->entity = $entity;
		$this->map = $entity->getMap();
		$this->logicalSchema = $entity->getLogicalSchema();
		$this->physicalSchema = $entity->getPhysicalSchema();

		if (($this->identifier = $this->logicalSchema->getIdentifier())) {
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
		Assert::isNotEmpty($this->identifier, 'identifierless orm entity');

		$entity = $this->identityMap->getLazy($id);

		// Avoid replace this code with $entity->fetch() because IdentifiableOrmEntity::fetch()
		// calls RdbmsDao::getById() to be filled with data
		if (!OrmUtils::isFetchedEntity($entity)) {
			try {
				$row = $this->getDB()->getRow(
					EntityQuery::create($this->entity)
						->setLimit(1)
						->where(
							Expression::eq($this->identifier, $id)
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
		Assert::isNotEmpty($this->identifier, 'identifierless orm entity');

		return $this->identityMap->getLazy($id);
	}

	/**
	 * Similar to IOrmEntityAccessor::getById() but for multiple entities at once. If one or more
	 * entities of the set not found, they won't be presented in the result set of entities
	 * @return array of {@link OrmEntity}
	 */
	function getByIds(array $ids)
	{
		Assert::isNotEmpty($this->identifier, 'identifierless orm entity');

		$fetched = array();
		$toFetch = array();
		$toFetchIds = array();

		foreach ($ids as $id) {
			$entity = $this->identityMap->getLazy($id);

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
					Expression::eq($this->identifier, $id)
				);
			}

			$newFetched = $this->getListBy($this->getDalExpression($fetchExpression));

			foreach ($newFetched as $entity) {
				unset ($toFetch[spl_object_hash($entity)]);
				$fetched[] = $entity;
			}

			// if there were some ID collisions - we should remove them from identityMap
			if (!empty($toFetch)) {
				foreach ($toFetch as $entity) {
					$this->identityMap->drop($entity->_getId());
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
	function getListBy(IDalExpression $condition)
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
		if ($this->identifier) {
			$id = $entity->_getId();
			$entity->_setId(null);
			$entity->fetch();
			$entity->_setId($id);
		}

		$rawValueSet = array();
		foreach ($this->logicalSchema->getProperties() as $propertyName => $property) {
			foreach ($property->getDBFields() as $columnName) {
				$rawValueSet[$propertyName][] = $dbValues[$columnName];
			}
		}

		$this->map->setRawValues($entity, $rawValueSet, $this->getFetchStrategy());


		if ($this->identityMap) {
			$this->identityMap->add($entity);
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
		Assert::isNotEmpty($this->identifier, 'identifierless orm entity');

		$affected = $this->sendQuery(
			DeleteQuery::create($this->physicalSchema->getDBTableName())
				->setExpression(
					$this->getDalExpression(
						Expression::eq(
							$this->identifier,
							$id
						)
					)
				)
		);

		$this->identityMap->drop($id);

		Assert::isTrue(($affected == 0) || ($affected == 1));

		return $affected == 1;
	}

	/**
	 * @return integer
	 */
	function dropByIds(array $ids)
	{
		Assert::isNotEmpty($this->identifier, 'identifierless orm entity');

		$expression = Expression::orChain();

		foreach ($ids as $id) {

			$this->identityMap->drop($id);

			$expression->add(
				Expression::eq(
					$this->identifier,
					$id
				)
			);
		}

		$affected = $this->sendQuery(
			DeleteQuery::create($this->physicalSchema->getDBTableName())
				->setExpression(
					$this->getDalExpression($expression)
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
			$this->identityMap->clean();
		}

		return $affected;
	}

	/**
	 * @return boolean
	 */
	function drop(IdentifiableOrmEntity $entity)
	{
		Assert::isNotEmpty($this->identifier, 'identifierless orm entity');

		return $this->dropById($entity->_getId());
	}

	/**
	 * @return SqlFieldValueCollection
	 */
	private function getFieldValueCollection(OrmEntity $entity)
	{
		$fvc = new SqlFieldValueCollection();
		foreach ($this->map->getRawValues($entity) as $propertyName => $rawValue) {
			$fvc->append(
				array_combine(
					$this->logicalSchema->getProperty($propertyName)->getDBFields(),
					$rawValue
				)
			);
		}

		return $fvc;
	}

	/**
	 * @return IDalExpression
	 */
	private function getDalExpression(IExpression $expression)
	{
		return EntityQuery::create($this->entity)
			->where($expression)
			->toDalExpression();
	}

	/**
	 * @return OrmEntity
	 */
	function save(OrmEntity $entity)
	{
		$fetched = true;

		if ($this->identifier) {
			$id = $entity->_getId();
			$idOrmPropertyType = $this->identifier->getType();

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

		if ($this->identifier && $fetched) {
			$affected = $this->sendQuery(
				new UpdateQuery(
					$this->physicalSchema->getDBTableName(),
					$fvc,
					$this->getDalExpression(
						Expression::eq($this->identifier, $entity->_getId())
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

		if ($this->identifier && !$fetched) {
			$id = $idOrmPropertyType->getGeneratedId(
				$this->getDB(),
				$this->physicalSchema->getDBTableName(),
				$this->identifier
			);

			$entity->_setId($id);
		}

		if ($this->identityMap) {
			$this->identityMap->add($entity);
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

		foreach ($this->physicalSchema->getDBFields() as $column) {
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