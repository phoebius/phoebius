<?php
/* ***********************************************************************************************
 *
 * Phoebius Framework
 *
 * **********************************************************************************************
 *
 * Copyright (c) 2009 Scand Ltd.
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
 * Contract for accessing IdentifiableOrmEntity
 *
 * TODO drop{by}() method family
 *
 * @ingroup Orm_Dao
 */
interface IOrmEntityAccessor
{
	/**
	 * Gets a raw database value of a cell.
	 *
	 * The resulting value is not mapped to any of entity properties.
	 * For straight mapping see IOrmEntityAccessor::getProperty()
	 *
	 * @warning A query MUST define only a single database column to be retrieved because only the first found
	 * cell is used as the resulting value.
	 *
	 * @warning A query MUST be able to limit the resulting rows count to a signle tuple (see ISqlSelectQuery::setLimit()).
	 *
	 * @throws CellNotFoundException thrown when no tuples where retrieved by the query.
	 * 				We cannot return NULL or FALSE because these values may be a valid values of the cell
	 * @param ISqlSelectQuery $query query to execute
	 * @return mixed a cell's raw value
	 */
	function getCell(ISqlSelectQuery $query);

	/**
	 * Gets an entity property mapped from a the database cells of a specific tuple.
	 *
	 * @warning A query MUST define for retrieval at least all those database columns that are needed to assemble an entity
	 *
	 * @warning A query MUST be able to limit the resulting rows count to a signle tuple (see ISqlSelectQuery::setLimit()).
	 *
	 * @param string $property name of an entity property
	 * @param ISqISqlSelectQuery $query query to execute
	 * @return mixed an OrmPropertyType-mapped value
	 */
	function getProperty($property, ISqlSelectQuery $query);

	/**
	 * Gets an IdentifiableOrmEntity object by id without fetching it from the DB (if not yet fetched).
	 *
	 * This method is optimized for assembling, i.e. if identified entity was fetched earlier
	 * then it would be used as the resulting object.
	 *
	 * The returned entity can be assembled with actual values using IdentifiableOrmEntity::fetch() method.
	 *
	 * Note that this method does not check the existance of an entity in the DB.
	 *
	 * @param mixed $id
	 * @return IdentifiableOrmEntity
	 */
	function getLazyEntityById($id);

	/**
	 * Gets the IdentifiableOrmEntity object by id.
	 *
	 * Entity assembling is performed according to the currently set FetchStrategy.
	 *
	 * @throws OrmEntityNotFoundException thrown when entity is not presented in the DB
	 *
	 * @param mixed $id
	 * @return IdentifiableOrmEntity
	 */
	function getEntityById($id);

	/**
	 * Gets the IdentifiableOrmEntity object by query.
	 *
	 * @warning A query MUST define for retrieval at least all those database columns that are needed to assemble an entity
	 *
	 * @warning A query MUST be able to limit the resulting rows count to a signle tuple (see ISqlSelectQuery::setLimit()).
	 *
	 * @throws OrmEntityNotFoundException thrown when entity is not presented in the DB
	 * @param ISqlSelectQuery $query query to execute
	 * @return IdentifiableOrmEntity
	 */
	function getEntity(ISqlSelectQuery $query);

	/**
	 * Gets a tuple of raw database values.
	 *
	 * Values presented in the resulting tuple are not mapped to an entity properties.
	 * For the straight mapping see IOrmEntityAccessor::getProperty()
	 *
	 * @warning A query MUST be able to limit the resulting rows count to a signle tuple (see ISqlSelectQuery::setLimit()).
	 *
	 * @throws RowNotFoundException thrown when no tuples where retrieved by the query.
	 * 				We actually can return NULL or FALSE because a tuple can be presented as
	 * 				array only, but we throw an explicit exception to be similar
	 * 				with IOrmEntityAccessor::getCell() method
	 * @param ISqlSelectQuery $query query to execute
	 * @return associative array that represents a tuple of raw database values
	 */
	function getRow(ISqlSelectQuery $query);

	/**
	 * Gets the set of IdentifiableOrmEntity objects by their identifiers
	 *
	 * Note that if some objects where not found, they silently won't be presented in the resulting
	 * set. No exception is thrown in this case (which is not similar
	 * to IOrmEntityAccessor::getById() logic).
	 *
	 * The resulting set is an associate array of identifiable objects where keys are identifiers
	 * casted to strings.
	 *
	 * Also not that this method DOES NOT GUARANTEE to return a set of objects in a order defined
	 * by the set of identifiers.
	 *
	 * @param array $ids associative array of identifiable objects
	 */
	function getByIds(array $ids);

	/**
	 * Gets the set IdentifiableOrmEntity objects by query.
	 *
	 * If query is not presented then ALL possible objects are fetched.
	 *
	 * @warning A query MUST define for retrieval at least all those database columns that are needed to assemble an entity
	 *
	 * @param ISqlSelectQuery $query optinal query to execute
	 * @return array set of IdentifiableOrmEntity objects
	 */
	function getList(ISqlSelectQuery $query = null);

	/**
	 * Gets a set of tuples of raw database values.
	 *
	 * Values presented in the resulting tuples are not mapped to an entity properties.
	 *
	 * If query is not presented then ALL tuples are retrieved.
	 *
	 * @param ISqlSelectQuery $query query to execute
	 * @return array an array of associative arrays that represent a tuples of raw database values
	 */
	function getRows(ISqlSelectQuery $query = null);

	/**
	 * Gets a set of raw database values of a specific cell.
	 *
	 * The resulting value is not mapped to any of entity properties.
	 * For straight mapping see IOrmEntityAccessor::getPropertyList()
	 *
	 * @warning A query MUST define only a single database column to retrieve because only the first found
	 * cell is used as the resulting value.
	 *
	 * @warning A query MUST be able to limit the resulting rows count to a signle tuple (see ISqlSelectQuery::setLimit()).
	 *
	 * @param ISqlSelectQuery $query query to execute
	 * @return array an array of raw values of a specific cell
	 */
	function getColumn(ISqlSelectQuery $query);

	/**
	 * Gets a set of entity properties mapped from a set of raw database cells.
	 *
	 * If query is not presented then a resulting property set would be collected from ALL
	 * objectes presented in the DB.
	 *
	 * @warning A query MUST define for retrieval at least all those database columns that are needed to assemble an entity
	 *
	 * @param string $property name of an entity property
	 * @param ISqISqlSelectQuery $query query to execute
	 * @return mixed an array of OrmPropertyType-mapped values
	 */
	function getPropertyList($property, ISqlSelectQuery $query = null);

	/**
	 * Executes a query against the entity's database and returns the number of affected rows
	 *
	 * @param ISqlQuery $query query to execute
	 * @return int number of affected rows, or NULL if query has no affect on rows
	 */
	function executeQuery(ISqlQuery $query);

	/**
	 * Drops the IdentifiableOrmEntity object by id from the database.
	 *
	 * This method does not perform a check whether the IdentifiableOrmEntity is presented
	 * in the database or not, but returns a boolean result specifying whether the drop has
	 * affection on object (if it was presented) or not.
	 *
	 * @param mixed $id
	 * @return boolean whether the drop has affection on object (if it was presented) or not.
	 */
	function dropEntityById($id);

	/**
	 * Save the IdentifiableOrmEntity object to the database.
	 *
	 * If entity's ID is not set then an entity is treated as a newly created object and
	 * a new tuple is inserted in the database.
	 *
	 * Otherwise,
	 *
	 * @param IdentifiableOrmEntity $entity entity to save
	 * @return boolean
	 */
	function saveEntity(IdentifiableOrmEntity $entity);
}

?>