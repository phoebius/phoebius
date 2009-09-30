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
 * Represents binary expression
 * @ingroup BaseExpression
 */
final class BinaryExpression implements IExpression
{
	/**
	 * @var mixed
	 */
	private $value;

	/**
	 * @var BinaryPredicate
	 */
	private $logic;

	function __construct($value, BinaryPredicate $logic)
	{
		$this->value = $value;
		$this->logic = $logic;
	}

	/**
	 * @return mixed
	 */
	function getValue()
	{
		return $this->value;
	}

	/**
	 * @return BinaryPredicate
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
		return new ExpressionType(ExpressionType::BINARY);
	}
}

?>