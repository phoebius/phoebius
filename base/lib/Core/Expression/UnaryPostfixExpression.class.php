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
class UnaryPostfixExpression implements IExpression
{
	/**
	 * @var mixed
	 */
	private $subject;

	/**
	 * @var UnaryPostfixPredicate
	 */
	private $logic;

	function __construct($subject, UnaryPostfixPredicate $logic)
	{
		$this->subject = $subject;
		$this->logic = $logic;
	}

	/**
	 * @return mixed
	 */
	function getSubject()
	{
		return $this->subject;
	}

	/**
	 * @return UnaryPostfixPredicate
	 */
	function getPredicate()
	{
		return $this->logic;
	}

	/**
	 * @return UnaryPostfixExpression
	 */
	function toExpression(IExpressionSubjectConverter $converter)
	{
		return new self(
			$converter->convert($this->subject, $this),
			$this->logic
		);
	}

	/**
	 * @return BinaryDalExpression
	 */
	function toDalExpression()
	{
		return new UnaryPostfixDalExpression($this);
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