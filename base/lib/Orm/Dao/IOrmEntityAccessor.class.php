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
	function getByCondition(IDalExpression $condition);

	/**
	 * @return array of {@link OrmEntity}
	 */
	function getListByCondition(IDalExpression $condition);

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
	function dropByCondition(IDalExpression $condition);

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