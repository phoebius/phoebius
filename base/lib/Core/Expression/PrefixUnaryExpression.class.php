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
	 * @var mixed
	 */
	private $subject;

	/**
	 * @var PrefixUnaryPredicate
	 */
	private $logic;

	function __construct(PrefixUnaryPredicate $logic, $subject)
	{
		$this->logic = $logic;
		$this->subject = $subject;
	}

	/**
	 * @return mixed
	 */
	function getSubject()
	{
		return $this->subject;
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