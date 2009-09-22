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
 * @ingroup Condition
 */
final class UnaryPostfixExpression implements ISqlLogicalExpression
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
	function __construct(ISqlValueExpression $subject, UnaryPostfixPredicate $logic)
	{
		Assert::isScalar($subject);

		$this->subject = $subject;
		$this->logic = $logic;
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