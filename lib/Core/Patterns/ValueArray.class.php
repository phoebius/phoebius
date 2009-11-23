<?php
/* ***********************************************************************************************
 *
 * Phoebius Framework
 *
 * **********************************************************************************************
 *
 * Copyright (c) 2009 phoebius.org
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
 * @see TypedValueArray as type-safe value list
 *
 * @ingroup Core_Patterns
 */
class ValueArray implements IteratorAggregate, Countable
{
	/**
	 * @var array of values
	 */
	private $values = array();

	/**
	 * @param array list of initial values to be imported
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
	 * @return ValueArray an object itself
	 */
	function merge(array $values)
	{
		foreach ($values as $value) {
			$this->appendValue($value);
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
}

?>