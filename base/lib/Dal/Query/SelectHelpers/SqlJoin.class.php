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
	 * @var SqlJoinMethod
	 */
	private $joinMethod;

	/**
	 * @param string $tableName
	 * @param SqlJoinMethod $joinMethod
	 */
	function __construct($tableName, SqlJoinMethod $joinMethod)
	{
		$this->tableName = $tableName;
		$this->joinMethod = $joinMethod;
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
	 * Returns the join method to be used when mergin the set of tables
	 * @return SqlJoinMethod
	 */
	protected function getJoinMethod()
	{
		return $this->joinMethod;
	}
}

?>