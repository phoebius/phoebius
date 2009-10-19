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
 * Represents a custom field=>value collection
 * @ingroup Core_Patterns
 */
class Collection implements IteratorAggregate
{
	/**
	 * Represents a field=>value collection
	 * @var array
	 */
	private $collection = array();

	/**
	 * Appends the collection of field=>value elements to the collection
	 * @throws ArgumentException if the value with the specified field is already added to
	 * 	the collection
	 * @param array of field=>value elements
	 * @return Collection an object itself
	 */
	function addCollection(array $collection)
	{
		foreach ($collection as $key => $value) {
			$this->collection[$key] = $value;
		}

		return $this;
	}

	/**
	 *
	 * @return Collection an object itself
	 */
	function setCollection(array $collection)
	{
		return $this->dropCollection()->addCollection($collection);
	}

	/**
	 * @return Collection
	 */
	function addPair($key, $value)
	{
		if (array_key_exists($key, $this->collection)) {
			throw new ArgumentException('field', 'already added');
		}

		$this->collection[$key] = $value;

		return $this;
	}

	/**
	 * Drops out the collection members
	 * @return Collection an object itself
	 */
	function dropCollection()
	{
		$this->collection = array();

		return $this;
	}

	/**
	 * Returns the value for the field name specified
	 * @throws ArgumentException if the field is not defined in the collection an thus value not found
	 * @return mixed
	 */
	function getValue($field)
	{
		if (!array_key_exists($field, $this->collection)) {
			throw new ArgumentException('field', 'field an its value not found in the collection');
		}

		return $this->collection[$field];
	}

	/**
	 * Gets the field list
	 * @return array
	 */
	function getFields()
	{
		return array_keys($this->collection);
	}

	/**
	 * Gets the value list
	 * @return array
	 */
	function getValues()
	{
		return array_values($this->collection);
	}

	/**
	 * Gets the collection represented as an array
	 * @return array
	 */
	function toArray()
	{
		return $this->collection;
	}

	/**
	 * @see IteratorAggregate::getIterator()
	 *
	 */
	function getIterator()
	{
		return new ArrayIterator($this->collection);
	}

	/**
	 * @return ArrayObject
	 */
	function toArrayObject()
	{
		return new ArrayObject($this->collection);
	}
}

?>