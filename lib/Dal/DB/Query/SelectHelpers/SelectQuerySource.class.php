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
 * Represents an abstract data source where the {@link SelectQuery} should be applied
 * @ingroup Dal_DB_Query
 * @aux
 */
final class SelectQuerySource implements ISqlValueExpression
{
	/**
	 * @var array
	 */
	private $joins = array();

	/**
	 * @var ISqlValueExpression
	 */
	private $source;

	function __construct(ISqlValueExpression $source)
	{
		$this->source = $source;
	}

	/**
	 * Adds a join condition to the target
	 * @return SelectQuerySource
	 */
	function join(SqlJoin $join)
	{
		$this->joins[] = $join;

		return $this;
	}

	/**
	 * Gets the set of {@link SqlJoin} joins
	 * @return array
	 */
	function getJoins()
	{
		return $this->joins;
	}

	/**
	 * Casts an object to the SQL dialect string
	 * @return string
	 */
	function toDialectString(IDialect $dialect)
	{
		$compiledSlices = array();

		$compiledSlices[] = $this->source->toDialectString($dialect);
		$compiledSlices[] = $this->compileJoins($dialect);

		$compiledString = join(' ', $compiledSlices);

		return $compiledString;
	}

	/**
	 * Casts the set of joins to the SQL
	 * @return string
	 */
	private function compileJoins(IDialect $dialect)
	{
		$compiledItems = array();
		foreach ($this->joins as $join) {
			$compiledItems[] = $join->toDialectString($dialect);
		}

		$compiledJoinString = join(' ', $compiledItems);
		return $compiledJoinString;
	}
}

?>