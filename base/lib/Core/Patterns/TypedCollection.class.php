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
abstract class TypedCollection extends Collection
{
	/**
	 * Determines whether the specified value is of valid type supported by the collection
	 * implementation
	 * @return boolean
	 */
	abstract protected function isValueOfValidType($value);

	/**
	 * @return Collection
	 */
	function addPair($key, $value)
	{
		if (!$this->isValueOfValidType($value)) {
			throw new ArgumentException('value', 'not of expected type');
		}

		return parent::addPair($key, $value);
	}
}

?>