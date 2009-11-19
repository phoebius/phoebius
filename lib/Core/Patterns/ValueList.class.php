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
 * Represents a list of values of similar type.
 *
 * To provide a precise type check, just overwrite ValueList::appendValue() and ValueList::prependValue()
 *
 * @see TypedValueList as type-safe value list
 *
 * @ingroup Core_Patterns
 */
class ValueList implements IteratorAggregate, Countable
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
		$this->append($values);
	}

	/**
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
	 * Gets the number of values in the list
	 * @return integer
	 */
	function getCount()
	{
		return sizeof($this->values);
	}

	/**
	 * Appends the value to the list
	 *
	 * @return ValueList
	 */
	function appendValue($value)
	{
		$this->values[] = $value;

		return $this;
	}

	/**
	 * Prepends the value to the list
	 *
	 * @return ValueList
	 */
	function prependValue($value)
	{
		array_unshift($this->values, $value);

		return $this;
	}

	/**
	 * Appends a list of values to the already added values
	 * @param array
	 * @return ValueList an object itself
	 */
	function append(array $values)
	{
		foreach ($values as $value) {
			$this->appendValue($value);
		}

		return $this;
	}

	/**
	 * Replaces the list of already added values with the new value list
	 *
	 * @param array new value list
	 * @return ValueList itself
	 */
	function replace(array $values)
	{
		$this
			->drop()
			->append($values);

		return $this;
	}

	/**
	 * Drops the list of already added values
	 *
	 * @return ValueList itself
	 */
	function drop()
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