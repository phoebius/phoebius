<?php
/* ***********************************************************************************************
 *
 * Phoebius Framework
 *
 * **********************************************************************************************
 *
 * Copyright (c) 2009 Scand Ltd.
 *
 * This program is free software; you can redistribute it and/or modify it under the terms
 * of the GNU Lesser General Public License as published by the Free Software Foundation;
 * either version 3 of the License, or (at your option) any later version.
 *
 * You should have received a copy of the GNU Lesser General Public License along with
 * this program; if not, see <http://www.gnu.org/licenses/>.
 *
 ************************************************************************************************/

/**
 * Represents a array of values of similar type.
 *
 * To provide a precise type check, just overwrite ValueArray::append() and ValueArray::prepend()
 *
 * Collection supports array-like assigments:
 * @code
 * $array = new ValueArray;
 * $array[] = "someval"; // same as $array->append("someval2");
 * @endcode
 *
 * @see TypedValueArray as type-safe value list
 *
 * @ingroup Core_Patterns
 */
class ValueArray implements IteratorAggregate, Countable, ArrayAccess
{
	/**
	 * @var array of values
	 */
	private $values = array();

	/**
	 * @param array list of initial values to be appended to array
	 */
	function __construct(array $values = array())
	{
		$this->merge($values);
	}

	/**
	 * Gets the first element of the array
	 *
	 * @throws StateException in case when empty
	 * @return mixed
	 */
	function getFirst()
	{
		if (!sizeof ($this->values)) {
			throw new StateException('empty array');
		}

		return reset($this->values);
	}

	/**
	 * Gets the last element of the array
	 *
	 * @throws StateException in case when empty
	 * @return mixed
	 */
	function getLast()
	{
		if (!sizeof ($this->values)) {
			throw new StateException('empty array');
		}

		return end($this->values);
	}

	/**
	 * Determines whether the array is empty
	 * @return boolean
	 */
	function isEmpty()
	{
		return $this->count() == 0;
	}

	/**
	 * Gets the number of elements in the array
	 *
	 * @return int
	 */
	function count()
	{
		return sizeof($this->values);
	}

	/**
	 * @see IteratorAggregate::getIterator()
	 * @return ArrayIterator
	 */
	function getIterator()
	{
		return new ArrayIterator($this->values);
	}

	/**
	 * Gets the number of elements in the array
	 * @return integer
	 */
	function getCount()
	{
		return sizeof($this->values);
	}

	/**
	 * Appends the value to the list
	 *
	 * @param mixed $value value to append to the array
	 * @return ValueArray
	 */
	function append($value)
	{
		$this->values[] = $value;

		return $this;
	}

	/**
	 * Prepends the value to the list
	 *
	 * @param mixed $value value to prepend to the array
	 * @return ValueArray
	 */
	function prepend($value)
	{
		array_unshift($this->values, $value);

		return $this;
	}

	/**
	 * Appends the list of values to the array
	 * @param array $values values to be appened to array
	 * @return ValueArray itself
	 */
	function merge(array $values)
	{
		foreach ($values as $value) {
			$this->append($value);
		}

		return $this;
	}

	/**
	 * Erases the array and append the list of values to the array
	 *
	 * @param array $values values to be appened to array
	 * @return ValueArray itself
	 */
	function replace(array $values)
	{
		$this
			->erase()
			->append($values);

		return $this;
	}

	/**
	 * Drops the list of already added values
	 *
	 * @return ValueArray itself
	 */
	function erase()
	{
		$this->values = array();

		return $this;
	}

	/**
	 * Returns the list of values
	 *
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

	/**
	 * @return array
	 */
	function toArrayObject()
	{
		return new ArrayObject($this->values);
	}

	function offsetExists($offset)
	{
		Assert::notImplemented('not implemented, and won\'t be');
	}

	function offsetGet($offset)
	{
		Assert::notImplemented('not implemented, and won\'t be');
	}

	function offsetSet($offset, $value)
	{
		Assert::isEmpty(
			$offset,
			'that\'s not an associative array! Do not use indexed when setting the value'
		);

		$this->append($value);
	}

	function offsetUnset($offset)
	{
		Assert::notImplemented('not implemented, and won\'t be');
	}
}

?>