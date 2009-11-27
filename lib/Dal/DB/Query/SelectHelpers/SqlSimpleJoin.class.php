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
 * Represents a SQL join with simple condition where the joining tables has the identical names
 * @ingroup Dal_DB_Query
 * @aux
 */
class SqlSimpleJoin extends SqlJoin
{
	/**
	 * @var SqlFieldArray
	 */
	private $identicalColumns;

	/**
	 * @param string $tableName
	 * @param string|null
	 * @param SqlJoinMethod $joinMethod
	 * @param SqlFieldArray $identicalColumns set of column names that should be used in joining
	 */
	function __construct($tableName, $alias, SqlJoinMethod $joinMethod, SqlFieldArray $identicalColumns)
	{
		$this->identicalColumns = $identicalColumns;

		parent::__construct($tableName, $alias, $joinMethod);
	}

	/**
	 * Casts an object to the SQL dialect string
	 * @return string
	 */
	function toDialectString(IDialect $dialect)
	{
		$compiledSlices = array();

		$compiledSlices[] = $this->getJoinMethod()->toDialectString($dialect);
		$compiledSlices[] = $dialect->quoteIdentifier($this->getTableName());
		if (($alias = $this->getTableAlias())) {
			$compiledSlices[] = $dialect->quoteIdentifier($alias);
		}
		$compiledSlices[] = 'USING';
		$compiledSlices[] = '(';
		$compiledSlices[] = $this->identicalColumns->toDialectString($dialect);
		$compiledSlices[] = ')';

		$compiledString = join(' ', $compiledSlices);
		return $compiledString;
	}
}

?>