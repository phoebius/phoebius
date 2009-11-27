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
 * Represents a SQL join with a complex condition
 * @ingroup Dal_DB_Query
 * @aux
 */
class SqlConditionalJoin extends SqlJoin
{
	/**
	 * @var IExpression
	 */
	private $condition;

	/**
	 * @param string $tableName
	 * @param string|null
	 * @param SqlJoinMethod $joinMethod
	 * @param IExpression $condition
	 */
	function __construct($tableName, $alias, SqlJoinMethod $joinMethod, IExpression $expression)
	{
		parent::__construct($tableName, $alias, $joinMethod);

		$this->expression = $expression;
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
		$compiledSlices[] = 'ON';
		$compiledSlices[] = '(';
		$compiledSlices[] = $this->expression->toDialectString($dialect);
		$compiledSlices[] = ')';

		$compiledString = join(' ', $compiledSlices);
		return $compiledString;
	}
}

?>