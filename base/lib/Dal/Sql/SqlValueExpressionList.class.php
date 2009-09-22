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
 * Represents a list value expressions (i.e. the list of {@link ISqlValueExpression})
 * @ingroup Sql
 * @see ISqlValueExpression
 */
class SqlValueExpressionList extends TypedValueList implements ISqlCastable
{
	/**
	 * Creates an instance of {@link SqlValueExpression}
	 * @param array $initialValueExpressions list of initial {@link ISqlValueExpression} to be imported
	 * @return SqlValueExpression
	 */
	static function create(array $initialValueExpressions = array())
	{
		return new self ($initialValueExpressions);
	}

	/**
	 * Append a value expression to the list
	 * @return SqlValueExpression an object itself
	 */
	function add(ISqlValueExpression $element)
	{
		$this->append($element);

		return $this;
	}

	/**
	 * Casts an object to the SQL dialect string
	 * @return string
	 */
	function toDialectString(IDialect $dialect)
	{
		$compiledSlices = array();
		foreach ($this->getList() as $element) {
			$compiledSlices[] = $element->toDialectString($dialect);
		}

		$compiledString = join(', ', $compiledSlices);

		return $compiledString;
	}

	/**
	 * Determines whether the specified value is of valid type supported by the list implementation
	 * @return boolean
	 */
	protected function isValueOfValidType($value)
	{
		return ($value instanceof ISqlValueExpression);
	}
}

?>