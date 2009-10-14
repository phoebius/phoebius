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
	 * @var array
	 */
	private $chain;

	/**
	 * @var ExpressionChainLogicalOperator
	 */
	private $logicalOperator;

	function __construct(ExpressionChain $expressionChain)
	{
		$this->logicalOperator = $expressionChain->getLogicalOperator();

		foreach ($expressionChain->getChain() as $item) {
			$this->chain[] = $item->toDalExpression();
		}
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

			$out = join($this->logicalOperator->toDialectString($dialect), $slices);

			return $out;
		}

		//nothin'
		return '';
	}
}

?>