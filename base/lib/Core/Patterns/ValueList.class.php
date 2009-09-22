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
 * Represents a list of values of similar type
 * @ingroup Patterns
 */
class ValueList
{
	/**
	 * @var array of values
	 */
	private $values = array();

	/**
	 * @param array $initialValues list of initial values to be imported
	 */
	function __construct(array $initialValues = array())
	{
		$this->appendList($initialValues);
	}

	/**
	 * Gets the number of values in list
	 * @return integer
	 */
	function getCount()
	{
		return sizeof($this->values);
	}

	/**
	 * Appends a list of values to the already added values
	 * @param array $values
	 * @return ValueList an object itself
	 */
	function appendList(array $values)
	{
		foreach ($values as $value) {
			$this->append($value);
		}

		return $this;
	}

	/**
	 * @return ValueList
	 */
	function append($value)
	{
		Assert::isTrue($this->isValueOfValidType($value));

		$this->values[] = $value;

		return $this;
	}

	/**
	 * @return ValueList
	 */
	function prepend($value)
	{
		Assert::isTrue($this->isValueOfValidType($value));

		array_unshift($this->values, $value);

		return $this;
	}

	/**
	 * Replaces a list of already added scalar values with the new value list
	 * @param array $set set of values
	 * @return ValueList an object itself
	 */
	function replaceList(array $values)
	{
		$this
			->dropList()
			->appendList($values);

		return $this;
	}

	/**
	 * Drops a list of already added values
	 * @return ValueList an object itself
	 */
	function dropList()
	{
		$this->values = array();

		return $this;
	}

	/**
	 * Returns the list of values
	 * @return array
	 */
	function getList()
	{
		return $this->values;
	}

	/**
	 * @return array
	 */
	function toArray()
	{
		return $this->values;
	}
}

?>