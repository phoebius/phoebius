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
	private $subject;

	/**
	 * @var mixed
	 */
	private $value;

	/**
	 * @var BinaryPredicate
	 */
	private $logic;

	function __construct($subject, BinaryPredicate $logic, $value)
	{
		$this->subject = $subject;
		$this->logic = $logic;
		$this->value = $value;
	}

	/**
	 * @return mixed
	 */
	function getSubject()
	{
		return $this->subject;
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