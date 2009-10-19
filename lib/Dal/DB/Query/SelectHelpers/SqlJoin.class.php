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
 * Represents a SQL join wrapper for {@link SelectQuery}
 * @ingroup Dal_DB_Query
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