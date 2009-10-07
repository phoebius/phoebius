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
 * @ingroup DalExpression
 */
class DalExpressionChain implements IDalExpression
{
	/**
	 * @var ExpressionChain
	 */
	private $expressionChain;

	function __construct(ExpressionChain $expressionChain)
	{
		$this->expressionChain = $expressionChain;
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

			return $out;
		}

		//nothin'
		return '';
	}
}

?>