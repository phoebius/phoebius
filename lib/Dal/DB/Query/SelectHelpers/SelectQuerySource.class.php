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
 * @internal
 */
abstract class SelectQuerySource
{
	/**
	 * @var string|null
	 */
	private $alias = null;

	/**
	 * @var array
	 */
	private $joins = array();

	/**
	 * Casts the source itself to the sql-compatible string using the {@link IDialect}
	 * specified
	 * @return string
	 */
	abstract protected function getCastedSourceExpression(IDialect $dialect);

	/**
	 * Gets the alias of the target, or NULL if not set
	 * @return scalar|null
	 */
	function getAlias()
	{
		return $this->alias;
	}

	/**
	 * Sets the alias of the target
	 * @return SelectQuerySource
	 */
	function setAlias($alias = null)
	{
		Assert::isScalarOrNull($alias);

		$this->alias = $alias;

		return $this;
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

		$compiledSlices[] = $this->getCastedSourceExpression($dialect);

		if (($alias = $this->getAlias())) {
			$compiledSlices[] = 'AS';
			$compiledSlices[] = $dialect->quoteIdentifier($alias);
		}

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