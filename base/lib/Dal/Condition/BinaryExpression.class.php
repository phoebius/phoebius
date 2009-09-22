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
 * @ingroup Condition
 */
final class BinaryExpression implements ISqlLogicalExpression
{
	/**
	 * @var SqlColumn
	 */
	private $field;

	/**
	 * @var ISqlCastable
	 */
	private $value;

	/**
	 * @var BinaryPredicate
	 */
	private $logic;

	/**
	 * @param SqlColumn $field
	 * @param ISqlValueExpression $value value to be compared. In most cases, {@link SqlValue} is
	 * 	needed here, but expressions are allowed to (e.g., {@link SelectQuery})
	 * @param BinaryPredicate $logic
	 */
	function __construct(SqlColumn $field, ISqlValueExpression $value, BinaryPredicate $logic)
	{
		$this->field = $field;
		$this->value = $value;
		$this->logic = $logic;
	}

	/**
	 * Casts an object to the SQL dialect string
	 * @return string
	 */
	function toDialectString(IDialect $dialect)
	{
		$compiledSlices = array();

		$compiledSlices[] = $this->field->toDialectString($dialect);
		$compiledSlices[] = $this->logic->toDialectString($dialect);
		$compiledSlices[] = '(';
		$compiledSlices[] = $this->value->toDialectString($dialect);
		$compiledSlices[] = ')';

		$compiledString = join(' ', $compiledSlices);

		return $compiledString;
	}
}

?>