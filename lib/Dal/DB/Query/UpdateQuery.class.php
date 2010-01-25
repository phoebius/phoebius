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
 * Represents a database query for chaning rows
 *
 * @ingroup Dal_DB_Query
 */
class UpdateQuery extends RowModificationQuery implements ISqlQuery
{
	/**
	 * @var IExpression
	 */
	private $condition;

	/**
	 * UpdateQuery static constructor
	 * @param string $table name of a table to update
	 * @return UpdateQuery
	 */
	static function create($table)
	{
		return new self ($table);
	}

	/**
	 * @param string $table name of a table to update
	 */
	function __construct($table)
	{
		Assert::isScalar($table);

		$this->table = $table;

		parent::__construct();
	}

	/**
	 * Sets the condition for rows that should be updated
	 *
	 * Only rows for which this expression returns true will be updated.
	 *
	 * @param IExpression $condition condition to be applied when updating rows
	 *
	 * @return UpdateQuery itself
	 */
	function setCondition(IExpression $condition = null)
	{
		$this->condition = $condition;

		return $this;
	}

	/**
	 * Gets the condition for rows that should be updated, if set.
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

		$querySlices[] = 'UPDATE';
		$querySlices[] = $dialect->quoteIdentifier($this->table);

		$querySlices[] = 'SET';
		$querySlices[] = $this->getRow()->toDialectString($dialect);

		if ($this->condition) {
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