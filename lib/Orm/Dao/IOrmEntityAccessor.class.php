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
interface IOrmEntityAccessor
{
	/**
	 * @return FetchStrategy
	 */
	function getFetchStrategy();

	/**
	 * @throws OrmEntityNotFoundException
	 * @return OrmEntity
	 */
	function getById($id);

	/**
	 * @return OrmEntity
	 */
	function getLazyById($id);

	/**
	 * Similar to IOrmEntityAccessor::getById() but for multiple entities at once. If one or more entities of
	 * the set not found, they won't be presented in the result set of entities
	 * @return array of {@link OrmEntity}
	 */
	function getByIds(array $ids);

	/**
	 * Returns all entities
	 */
	function getList();

	/**
	 * @throws OrmEntityNotFoundException
	 * @return OrmEntity
	 */
	function getBy(IExpression $condition);

	/**
	 * @return array of {@link OrmEntity}
	 */
	function getListBy(IExpression $condition);

	/**
	 * @throws OrmEntityNotFoundException
	 * @return OrmEntity
	 */
	function getByQuery(ISqlSelectQuery $query);

	/**
	 * @return array
	 */
	function getListByQuery(ISqlSelectQuery $query);

	/**
	 * @return array
	 */
	function getCustomRowByQuery(ISqlSelectQuery $query);

	/**
	 * @return array
	 */
	function getCustomRowsByQuery(ISqlSelectQuery $query);

	/**
	 * Returns the number of affected rows
	 * @return integer
	 */
	function sendQuery(ISqlQuery $query);

	/**
	 * @return boolean
	 */
	function dropById($id);

	/**
	 * @return integer
	 */
	function dropByIds(array $id);

	/**
	 * @return integer
	 */
	function dropBy(IExpression $condition);

	/**
	 * @return boolean
	 */
	function drop(IdentifiableOrmEntity $entity);

	/**
	 * @return OrmEntity
	 */
	function save(OrmEntity $entity);
}

?>