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
 * Represents an abstract data source where the {@link SelectQuery} should be applied
 * @ingroup SelectQueryHelpers
 * @internal
 */
abstract class SelectQuerySource implements ISqlCastable
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