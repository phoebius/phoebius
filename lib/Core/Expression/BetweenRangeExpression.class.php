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
class BetweenRangeExpression implements IExpression
{
	/**
	 * @var mixed
	 */
	private $subject;

	/**
	 * @var mixed
	 */
	private $from;

	/**
	 * @var mixed
	 */
	private $to;

	function __construct($subject, $from, $to)
	{
		$this->subject = $subject;
		$this->from = $from;
		$this->to = $to;
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
	 * @return BetweenRangeExpression
	 */
	function toExpression(IExpressionSubjectConverter $converter)
	{
		return new self(
			$converter->convert($this->subject, $this),
			$converter->convert($this->from, $this),
			$converter->convert($this->to, $this)
		);
	}

	/**
	 * @return BinaryDalExpression
	 */
	function toDalExpression()
	{
		return new BetweenRangeDalExpression($this);
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