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
	private $chain;

	function __construct(ExpressionChain $expressionChain)
	{
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

			$out = join($this->chain->getPredicate()->toDialectString($dialect), $slices);

			return $out;
		}

		//nothin'
		return '';
	}
}

?>