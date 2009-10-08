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
 * @ingroup DalExpression
 */
class InSetDalExpression implements IDalExpression
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

	function __construct(InSetExpression $expression)
	{
		$this->field = $expression->getSubject();
		$this->set = $expression->getSet();
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
		$compiledSlices[] = $this->field->toDialectString($dialect);
		$compiledSlices[] = ')';
		$compiledSlices[] = $this->logic->toDialectString($dialect);
		$compiledSlices[] = '(';
		$compiledSlices[] = $this->set->toDialectString($dialect);
		$compiledSlices[] = ')';

		$compiledString = join(' ', $compiledSlices);

		return $compiledString;
	}
}

?>