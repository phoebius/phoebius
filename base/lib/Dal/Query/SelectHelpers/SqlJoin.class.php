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
 * Represents a SQL join wrapper for {@link SelectQuery}
 * @ingroup SelectQueryHelpers
 * @internal
 */
abstract class SqlJoin implements ISqlCastable
{
	/**
	 * @var string
	 */
	private $tableName;
	/**
	 * @var string
	 */
	private $alias;

	/**
	 * @var SqlJoinMethod
	 */
	private $joinMethod;

	/**
	 * @param string $tableName
	 * @param string|null
	 * @param SqlJoinMethod $joinMethod
	 */
	function __construct($tableName, $alias, SqlJoinMethod $joinMethod)
	{
		$this->tableName = $tableName;
		$this->joinMethod = $joinMethod;
		$this->alias = $alias;
	}

	/**
	 * Returns the table name that is to be joined
	 * @return string
	 */
	protected function getTableName()
	{
		return $this->tableName;
	}

	/**
	 * Returns the table alias that is to be joined
	 * @return string
	 */
	protected function getTableAlias()
	{
		return $this->alias;
	}

	/**
	 * Returns the join method to be used when mergin the set of tables
	 * @return SqlJoinMethod
	 */
	protected function getJoinMethod()
	{
		return $this->joinMethod;
	}
}

?>