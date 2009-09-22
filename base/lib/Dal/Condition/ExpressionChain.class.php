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
 * Represents an expression chain
 * @ingroup Condition
 */
final class ExpressionChain implements ISqlLogicalExpression
{
	/**
	 * @var ExpressionChainPredicate
	 */
	private $predicate;

	/**
	 * @var array
	 */
	private $chain = array();

	function __construct(ExpressionChainPredicate $predicate)
	{
		$this->predicate = $predicate;
	}

	/**
	 * Adds the expression to the expression chain
	 * @return ExpressionChain
	 */
	function add(ISqlLogicalExpression $expression)
	{
		$this->chain[] = $expression;

		return $this;
	}

	/**
	 * @return boolean
	 */
	function isEmpty()
	{
		return empty($this->chain);
	}

	/**
	 * Casts an object to the SQL dialect string
	 * @return string|null
	 */
	function toDialectString(IDialect $dialect)
	{
		if (!empty($this->chain)) {
			$slices = array();

			foreach ($this->chain as $expression) {
				$sqlExpression = $expression->toDialectString($dialect);

				if (empty($sqlExpression)) {
					continue;
				}

				$slices[] = ' ( ' . $sqlExpression . ' ) ';
			}

			$out = join($this->predicate->toDialectString($dialect), $slices);

			if (sizeof($slices) == 1) {
				return $out;
			}

			return ' ( ' . $out . ' ) ';
		}

		//nothin'
		return null;
	}
}

?>