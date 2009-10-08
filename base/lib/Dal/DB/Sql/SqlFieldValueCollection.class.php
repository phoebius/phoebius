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
 * Represetns a field=>value collection. Field is a name of the column without specifying
 * its belonging to a table
 * @ingroup Sql
 */
class SqlFieldValueCollection extends TypedCollection implements ISqlCastable
{
	/**
	 * Adds a field=>value pair to the collection
	 * @throws ArgumentException if the value with the specified field is already added to
	 * 	the collection
	 * @return SqlFieldValueCollection an object itself
	 */
	function add($field, SqlValue $value)
	{
		$this->addPair($field, $value);

		return $this;
	}

	/**
	 * Returns the value for the field name specified
	 * @throws ArgumentException if the field is not defined in the collection an thus value not found
	 * @return SqlValue
	 */
	function getValue($field)
	{
		return parent::getValue($field);
	}

	/**
	 * Casts an object to the SQL dialect string
	 * @return string
	 */
	function toDialectString(IDialect $dialect)
	{
		$fieldValueCompiledPairs = array();

		foreach ($this->toArray() as $field => $value) {
			$fieldValueCompiledPairs[] =
				  $dialect->quoteIdentifier($field) . ' = '
				. $value->toDialectString($dialect);
		}

		$compiledCollection = join(', ', $fieldValueCompiledPairs);
		return $compiledCollection;
	}

	/**
	 * Determines whether the specified value is of valid type supported by the collection
	 * implementation
	 * @return boolean
	 */
	protected function isValueOfValidType($value)
	{
		return ($value instanceof SqlValue);
	}
}

?>