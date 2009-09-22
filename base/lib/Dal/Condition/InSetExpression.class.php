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
 * Represents the IN expression used in query logic
 * @ingroup Condition
 */
final class InSetExpression implements ISqlLogicalExpression
{
	/**
	 * @var SqlColumn
	 */
	private $field;

	/**
	 * @var ISqlCastable
	 */
	private $set;

	/**
	 * @var InSetPredicate
	 */
	private $logic;

	/**
	 * @param SqlColumn $field field which value to be searched inside set
	 * @param ISqlValueExpression $set representing a set of values, In most cases,
	 * 	{@link SqlValueList} is needed here, but can also be used other Sql-compatible
	 * 	expressions, like {@link SelectQuery}
	 */
	function __construct(SqlColumn $field, ISqlValueExpression $set, InSetPredicate $logic)
	{
		$this->field = $field;
		$this->set = $set;
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
		$compiledSlices[] = $this->set->toDialectString($dialect);
		$compiledSlices[] = ')';

		$compiledString = join(' ', $compiledSlices);

		return $compiledString;
	}
}

?>