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
 * @ingroup Condition
 */
final class PrefixUnaryExpression implements ISqlLogicalExpression
{
	/**
	 * @var ISqlCastable
	 */
	private $subject;

	/**
	 * @var PrefixUnaryPredicate
	 */
	private $logic;

	/**
	 * @param PrefixUnaryPredicate $logic
	 * @param ISqlValueExpression $subject probably, {@link SqlColumn}, but can be either
	 * 	{@link SelectQuery} or any other sql expression
	 */
	function __construct(PrefixUnaryPredicate $logic, ISqlValueExpression $subject)
	{
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

		$compiledSlices[] = $this->logic->toDialectString($dialect);
		$compiledSlices[] = '(';
		$compiledSlices[] = $this->subject->toDialectString($dialect);
		$compiledSlices[] = ')';

		$compiledString = join(' ', $compiledSlices);

		return $compiledString;
	}
}

?>