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
 * Represents a source for selection
 *
 * Source is represented as ISqlValueExpression and can be the following:
 * - SqlIdentifier to specify tables
 * - SqlFunction to aggregate or produce the results
 * - Expression to produce the results
 * - SelectQuery as a sub-query
 * - AliasedSqlValueExpression to label the source
 *
 * @see SelectQuery::addSource()
 *
 * @ingroup Dal_DB_Query
 * @aux
 */
final class SelectQuerySource implements ISqlValueExpression
{
	/**
	 * @var array of SqlJoin
	 */
	private $joins = array();

	/**
	 * @var ISqlValueExpression
	 */
	private $source;

	/**
	 * @param ISqlValueExpression $source expression to use as source
	 */
	function __construct(ISqlValueExpression $source)
	{
		$this->source = $source;
	}

	/**
	 * Joins a source
	 * @return SelectQuerySource
	 */
	function join(SqlJoin $join)
	{
		$this->joins[] = $join;

		return $this;
	}

	/**
	 * Get joined sources
	 * @return array of SqlJoin
	 */
	function getJoins()
	{
		return $this->joins;
	}

	function toDialectString(IDialect $dialect)
	{
		$compiledSlices = array();

		$compiledSlices[] = $this->source->toDialectString($dialect);
		$compiledSlices[] = $this->compileJoins($dialect);

		$compiledString = join(' ', $compiledSlices);

		return $compiledString;
	}

	/**
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