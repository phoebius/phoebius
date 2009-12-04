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
	private $table;

	/**
	 * @var IExpression
	 */
	private $condition;

	/**
	 * DeleteQuery static constructor
	 * @param string $table name of a table to delete rows from
	 * @return DeleteQuery
	 */
	static function create($table)
	{
		return new self($table);
	}

	/**
	 * @param string $table name of a table to delete rows from
	 */
	function __construct($table)
	{
		Assert::isScalar($table);

		$this->table = $table;
	}

	/**
	 * Sets the condition for rows that should be deleted
	 *
	 * Only rows for which this expression returns true will be deleted.
	 *
	 * @param IExpression $condition condition to be applied when deleted rows
	 *
	 * @return DeleteQuery itself
	 */
	function setCondition(IExpression $condition = null)
	{
		$this->condition = $condition;

		return $this;
	}

	/**
	 * Gets the condition for rows that should be deleted, if set.
	 *
	 * @return IExpression|null
	 */
	function getCondition()
	{
		return $this->condition;
	}

	function toDialectString(IDialect $dialect)
	{
		$querySlices = array();

		$querySlices[] = 'DELETE FROM';
		$querySlices[] = $dialect->quoteIdentifier($this->table);

		if ($this->entityQuery) {
			$querySlices[] = 'WHERE';
			$querySlices[] = $this->condition->toDialectString($dialect);
		}

		$compiledQuery = join(' ', $querySlices);
		return $compiledQuery;
	}

	function getPlaceholderValues(IDialect $dialect)
	{
		return array ();
	}
}

?>