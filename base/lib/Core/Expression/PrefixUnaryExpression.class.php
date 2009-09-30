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
 * Represents a unary prefix expression
 * @ingroup BaseExpression
 */
final class PrefixUnaryExpression implements IExpression
{
	/**
	 * @var PrefixUnaryPredicate
	 */
	private $logic;

	function __construct(PrefixUnaryPredicate $logic)
	{
		$this->logic = $logic;
	}

	/**
	 * @return PrefixUnaryPredicate
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
		return new ExpressionType(ExpressionType::PREFIX_UNARY);
	}
}

?>