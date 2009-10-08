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
 * @ingroup DalExpression
 */
class UnaryPostfixDalExpression implements IDalExpression
{
	/**
	 * @var ISqlCastable
	 */
	private $subject;

	/**
	 * @var UnaryPostfixPredicate
	 */
	private $logic;

	/**
	 * @param ISqlValueExpression $subject probably, {@link SqlColumn}, but can be either
	 * 	{@link SelectQuery} or any other sql expression
	 * @param UnaryPostfixPredicate $logic
	 */
	function __construct(UnaryPostfixExpression $expression)
	{
		$this->subject = $expression->getSubject();
		$this->logic = $expression->getPredicate();
	}

	/**
	 * Casts an object to the SQL dialect string
	 * @return string
	 */
	function toDialectString(IDialect $dialect)
	{
		$compiledSlices = array();

		$compiledSlices[] = '(';
		$compiledSlices[] = $this->subject->toDialectString($dialect);
		$compiledSlices[] = ')';
		$compiledSlices[] = $this->logic->toDialectString($dialect);

		$compiledString = join(' ', $compiledSlices);

		return $compiledString;
	}
}

?>