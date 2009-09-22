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
 * @ingroup Condition
 */
final class BetweenRangeExpression implements ISqlLogicalExpression
{
	/**
	 * @var SqlColumn
	 */
	private $field;

	/**
	 * @var ISqlCastable
	 */
	private $from;

	/**
	 * @var ISqlCastable
	 */
	private $to;

	/**
	 * @param SqlColumn $field
	 * @param ISqlValueExpression $from starting value in range. In most cases, {@link SqlValue} is
	 * 	needed here, but expressions are allowed to (e.g., {@link SelectQuery})
	 * @param ISqlValueExpression $to ending value in range. In most cases, {@link SqlValue} is
	 * 	needed here, but expressions are allowed to (e.g., {@link SelectQuery})
	 */
	function __construct(SqlColumn $field, ISqlValueExpression $from, ISqlValueExpression $to)
	{
		$this->from = $from;
		$this->to = $to;
		$this->field = $field;
	}

	/**
	 * Casts an object to the SQL dialect string
	 * @return string
	 */
	function toDialectString(IDialect $dialect)
	{
		$compiledSlices = array();

		$compiledSlices[] = $this->field->toDialectString($dialect);
		$compiledSlices[] = 'BETWEEN';
		$compiledSlices[] = '(';
		$compiledSlices[] = $this->from->toDialectString($dialect);
		$compiledSlices[] = ')';
		$compiledSlices[] = 'AND';
		$compiledSlices[] = '(';
		$compiledSlices[] =  $this->to->toDialectString($dialect);
		$compiledSlices[] = ')';

		$compiledString = join(' ', $compiledSlices);

		return $compiledString;
	}
}

?>