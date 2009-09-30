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
 * Represents a postfix unary expression
 * @ingroup BaseExpression
 */
final class UnaryPostfixExpression implements IExpression
{
	/**
	 * @var UnaryPostfixPredicate
	 */
	private $logic;

	function __construct(UnaryPostfixPredicate $logic)
	{
		$this->logic = $logic;
	}

	/**
	 * @return UnaryPostfixPredicate
	 */
	function getPredicate()
	{
		return $this->logic;
	}

	/**
	 * @return ExpressionType
	 */
	function getExpressionType()
	{
		return new ExpressionType(ExpressionType::UNARY_POSTFIX);
	}
}

?>