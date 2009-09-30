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
 * Represents an range expression
 * @ingroup BaseExpression
 */
final class BetweenRangeExpression implements IExpression
{
	/**
	 * @var mixed
	 */
	private $from;

	/**
	 * @var mixed
	 */
	private $to;

	function __construct($from, $to)
	{
		$this->from = $from;
		$this->to = $to;
	}

	/**
	 * @return mixed
	 */
	function getFrom()
	{
		return $this->from;
	}

	/**
	 * @return mixed
	 */
	function getTo()
	{
		return $this->to;
	}

	/**
	 * @return ExpressionType
	 */
	function getExpressionType()
	{
		return new ExpressionType(ExpressionType::BETWEEN);
	}
}

?>