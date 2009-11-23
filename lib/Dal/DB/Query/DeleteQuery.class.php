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
 * Represents a database query for deleting rows
 * @ingroup Dal_DB_Query
 */
class DeleteQuery implements ISqlQuery
{
	/**
	 * @var string
	 */
	private $tableName;

	/**
	 * @var IExpression
	 */
	private $entityQuery;

	/**
	 * Creates an instance of {@link DeleteQuery}
	 * @param string $table
	 * @return DeleteQuery
	 */
	static function create($table, IExpression $expression = null)
	{
		return new self($table, $expression);
	}

	/**
	 * @param string $table
	 */
	function __construct($tableName, IExpression $expression = null)
	{
		Assert::isScalar($tableName);

		$this->tableName = $tableName;
		$this->setExpression($expression);
	}

	/**
	 * Sets the query condition to fill the `WHERE` clause
	 * @return DeleteQuery an object itself
	 */
	function setExpression(IExpression $expression = null)
	{
		$this->entityQuery = $expression;

		return $this;
	}

	/**
	 * Gets the query condition or null if IExpression is not set
	 * @return IExpression|null
	 */
	function getExpression()
	{
		return $this->entityQuery;
	}

	/**
	 * Gets the table name where the query should be performed
	 * @return string
	 */
	function getTableName()
	{
		return $this->tableName;
	}

	/**
	 * Casts an object to the plain string SQL query with database dialect
	 * @return string
	 */
	function toDialectString(IDialect $dialect)
	{
		$querySlices = array();

		$querySlices[] = 'DELETE FROM';
		$querySlices[] = $dialect->quoteIdentifier($this->tableName);

		if ($this->entityQuery) {
			$querySlices[] = 'WHERE';
			$querySlices[] = $this->entityQuery->toDialectString($dialect);
		}

		$compiledQuery = join(' ', $querySlices);
		return $compiledQuery;
	}

	/**
	 * @see ISqlQuery::getCastedParameters()
	 *
	 * @param IDialect $dialect
	 * @return array
	 */
	function getCastedParameters(IDialect $dialect)
	{
		return array ();
	}
}

?>