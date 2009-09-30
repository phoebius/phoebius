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
 * @ingroup DalExpression
 */
class BetweenRangeDalExpression implements IDalExpression
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

	function __construct(SqlColumn $field, BetweenRangeExpression $expression)
	{
		$this->field = $field;
		$this->to = $expression->getTo();
		$this->from= $expression->getFrom();
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