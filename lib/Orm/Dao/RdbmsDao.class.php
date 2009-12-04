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
 * Represents a layer between ORM and RDBMS
 *
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
	 * @var OrmIdentityMap
	 */
	private $identityMap;

	/**
	 * @var OrmProperty
	 */
	private $identifier;

	/**
	 * @var IQueryable
	 */
	private $entity;

	/**
	 * @param DB $db RDBMS to use
	 * @param IQueryable $entity ORM-related entity representation
	 */
	function __construct(DB $db, IQueryable $entity)
	{
		$this->db = $db;
		$this->entity = $entity;
		$this->map = $entity->getMap();
		$this->logicalSchema = $entity->getLogicalSchema();
		$this->physicalSchema = $entity->getPhysicalSchema();
		$this->identifier = $this->logicalSchema->getIdentifier();
		$this->identityMap = new OrmIdentityMap($this->logicalSchema);
	}

	/**
	 * Gets the database used to obtain entity data.
	 *
	 * If the connection to the database is not yet established, it is invoked.
	 *
	 * @return DB
	 */
	function getDB()
	{
		return $this->db->connect(false);
	}

	/**
	 * Sets the fetch strategy to use
	 *
	 * @return RdbmsDao
	 */
	function setFetchStrategy(FetchStrategy $fs)
	{
		$this->fetchStrategy = $fs;

		return $this;
	}

	/**
	 * Gets the current fetching strategy
	 *
	 * @return FetchStrategy
	 */
	function getFetchStrategy()
	{
		if (!$this->fetchStrategy) {
			$this->fetchStrategy = FetchStrategy::cascade();
		}

		return $this->fetchStrategy;
	}

	function getCell(ISqlSelectQuery $query)
	{
		$query->setLimit(1);

		return $this->getDB()->getCell($query);
	}

	function getProperty($property, ISqlSelectQuery $query)
	{
		$query->setLimit(1);

		$row = $this->getDB()->getRow($query);

		$object =
			$this->logicalSchema
				->getProperty($property)
				->getType()
				->assemble($row, $this->getFetchStrategy());

		return $object;
	}

	function getLazyEntityById($id)
	{
		return $this->identityMap->getLazy($id);
	}

	function getEntityById($id)
	{
		$entity = $this->getLazyEntityById($id);

		if (!$entity->isFetched()) {
			$query =
				EntityQuery::create($this->entity)
					->setLimit(1)
					->where(Expression::eq($this->identifier, $id));

			try {
				$row = $this->getRow($query);
			}
			catch (RowNotFoundException $e) {
				throw new OrmEntityNotFoundException($this->entity, $id.' is not presented');
			}

			$this->map->assemble($entity, $row, $this->getFetchStrategy());
		}

		return $entity;
	}

	function getEntity(ISqlSelectQuery $query)
	{
		try {
			$row = $this->getRow($query);
		}
		catch (RowNotFoundException $e) {
			throw new OrmEntityNotFoundException($this->entity, 'query returned zero rows');
		}

		$entity = $this->logicalSchema->getNewEntity();
		$this->map->assemble($entity, $row, $this->getFetchStrategy());

		$this->identityMap->add($entity);

		return $entity;
	}

	function getRow(ISqlSelectQuery $query)
	{
		return $this->getDB()->getRow($query);
	}

	function getByIds(array $ids)
	{
		$entitySet = array();
		$toFetch = array();

		foreach ($ids as $id) {
			$entity = $this->identityMap->getLazy($id);

			if (!$entity->isFetched()) {
				$toFetch[$id] = $entity;
			}
			else {
				$entitySet[$id] = $entity;
			}
		}

		if (!empty($toFetch)) {
			$query =
				EntityQuery::create($this->entity)
					->where(
						Expression::in($this->identifier, array_keys($toFetch))
					);

			$fetched = $this->getList($query);

			foreach ($fetched as $entity) {
				$id = $entity->_getId();
				$entitySet[$id] = $entity;
				unset ($toFetch[$id]);
			}

			// if there were some ID collisions - we should remove them from identityMap
			if (!empty($toFetch)) {
				foreach ($toFetch as $entity) {
					$this->identityMap->drop($entity->_getId());
				}
			}
		}

		return $entitySet;
	}

	function getList(ISqlSelectQuery $query = null)
	{
		$rows = $this->getRows($query);

		$entitySet = array ();
		foreach ($rows as $row) {
			$entity = $this->map->assemble($this->logicalSchema->getNewEntity(), $row, $this->getFetchStrategy());
			$entitySet[] = $entity;
			$this->identityMap->add($entity);
		}

		return $entitySet;
	}

	function getRows(ISqlSelectQuery $query = null)
	{
		if (!$query) {
			$query = EntityQuery::create($this->entity);
		}

		$rows = $this->getDB()->getRows($query);

		return $rows;
	}

	function getColumn(ISqlSelectQuery $query)
	{
		return $this->getDB()->getColumn($query);
	}

	function getPropertyList($property, ISqlSelectQuery $query = null)
	{
		$property = $this->logicalSchema->getProperty($property);
		$type = $property->getType();

		if (!$query) {
			$query =
				EntityQuery::create($this->entity)
					->get(Projection::property($property));
		}

		$rows = $this->getRows($query);
		$propertySet = $type->assebmleSet($rows, $this->getFetchStrategy());

		return $propertySet;
	}

	function executeQuery(ISqlQuery $query)
	{
		$result = $this->getDB()->sendQuery($query, false);
		return $this->getDB()->getAffectedRowsNumber($result);
	}

	function dropEntityById($id)
	{
		$query =
			EntityQuery::create($this->entity)
				->where(
					Expression::eq($this->identifier, $id)
				);

		return $this->executeQuery($query);
	}

	function saveEntity(IdentifiableOrmEntity $entity)
	{
		$id = $entity->_getId();

		$entity->_setId(null);
		$entity->fetch();
		$entity->_setId($id);

		if ($id) {
			$updated = $this->update($entity);

			if ($updated) {
				return true;
			}
		}

		try {
			$this->insert($entity);
		}
		catch (UniqueViolationException $e) {
			if (!$id) {
				throw $e;
			}
		}

		return true;
	}

	private function insert(IdentifiableOrmEntity $entity)
	{
		$id = $entity->_getId();
		$idType = $this->identifier->getType();

		$generator =
			!$id && $idType instanceof IOrmEntityIdGenerator
				? $idType->getIdGenerator($entity)
				: null;

		$generatorType =
			$generator
				? $generator->getType()
				: null;

		if ($generatorType && $generatorType->isPre()) {
			$id = $generator->generate($entity);
			if ($id) {
				$entity->_setId($id);
			}
		}

		$affected = $this->executeQuery(
			InsertQuery::create(
				$this->physicalSchema->getTable()
			)
			->setValues($this->map->disassemble($entity))
		);

		if ($generatorType && $generatorType->isPost()) {
			$id = $generator->generate($entity);
			if ($id) {
				$entity->_set($id);
			}
		}

		$this->identityMap->add($entity);
	}

	private function update(IdentifiableOrmEntity $entity)
	{
		$affected = $this->executeQuery(
			UpdateQuery::create(
				$this->physicalSchema->getTable()
			)
			->setValues($this->map->disassemble($entity))
			->setCondition(
				EntityQuery::create($this->entity)
					->where(Expression::eq($this->identifier, $entity->_getId()))
					->toExpression()
			)
		);

		return $affected > 0;
	}
}

?>