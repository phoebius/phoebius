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
 * @ingroup DalExpression
 */
class BinaryDalExpression implements IDalExpression
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

	function __construct(BinaryExpression $expression)
	{
		$this->field = $expression->getSubject();
		$this->value = $expression->getValue();
		$this->logic = $expression->getPredicate();
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