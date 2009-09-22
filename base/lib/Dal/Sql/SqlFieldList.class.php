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
 * Represents a list of fields, that can be casted to SQL value set
 * @ingroup Sql
 */
class SqlFieldList extends TypedValueList implements ISqlCastable
{
	/**
	 * Creates an instance of {@link SqlFieldList}
	 * @param array $initialFields initial scalar names of the fields to be imported
	 * @return SqlFieldList
	 */
	static function create(array $initialFields = array())
	{
		return new self($initialFields);
	}

	/**
	 * Append a scalar name of the field to the list
	 * @param scalar $field
	 * @return SqlFieldList an object itself
	 */
	function add($fieldName)
	{
		$this->addTransparent($fieldName);

		return $this;
	}

	/**
	 * Casts an object to the SQL dialect string
	 * @return string
	 */
	function toDialectString(IDialect $dialect)
	{
		$quotedFields = array();
		foreach ($this->getList() as $field) {
			$quotedFields[] = $dialect->quoteIdentifier($field);
		}

		$joinedFields = join(', ', $quotedFields);

		return $joinedFields;
	}

	/**
	 * Determines whether the specified value is of valid type supported by the list implementation
	 * @return boolean
	 */
	protected function isValueOfValidType($value)
	{
		return is_scalar($value);
	}
}

?>