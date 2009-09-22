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
 * @ingroup Patterns
 */
abstract class TypedValueList extends ValueList
{
	/**
	 * Determines whether the specified value is of valid type supported by the list implementation
	 * @return boolean
	 */
	abstract protected function isValueOfValidType($value);

	/**
	 * @return ValueList
	 */
	function append($value)
	{
		if (!$this->isValueOfValidType($value)) {
			throw new ArgumentException('value', 'not of expected type');
		}

		return parent::append($value);
	}

	/**
	 * @return ValueList
	 */
	function prepend($value)
	{
		if (!$this->isValueOfValidType($value)) {
			throw new ArgumentException('value', 'not of expected type');
		}

		return parent::prepend($value);
	}
}

?>