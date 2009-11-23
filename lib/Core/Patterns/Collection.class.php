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
 * Represents a custom key=>value collection.
 *
 * To provide a precise type check, just overwrite Collection::set().
 *
 * @see TypedCollection a type-safe collection implementation
 *
 * @ingroup Core_Patterns
 */
class Collection implements IteratorAggregate, ArrayAccess, Countable
{
	/**
	 * @var array
	 */
	private $collection = array();

	/**
	 * @param array set of values to be appened to a collection
	 */
	function __construct(array $values = array())
	{
		$this->append($values);
	}

	function offsetExists($offset)
	{
		return $this->has($offset);
	}

	function offsetGet($offset)
	{
		return $this->get($offset);
	}

	function offsetSet($offset, $value)
	{
		$this->set($offset, $value);
	}

	function offsetUnset($offset)
	{
		$this->drop($offset);
	}

	/**
	 * Gets the number of elements in the collection
	 *
	 * @return int
	 */
	function count()
	{
		return sizeof($this->collection);
	}

	/**
	 * Gets the number of elements in the collection
	 *
	 * @return int
	 */
	function getCount()
	{
		return sizeof($this->collection);
	}

	/**
	 * Determines whether the element is presented in the collection
	 *
	 * @param string $key name of the element
	 * @return boolean
	 */
	function has($key)
	{
		return isset($this->collection[$key]);
	}

	/**
	 * Gets the element by it's name
	 *
	 * @param string $key name of the element
	 * @return mixed
	 */
	function get($key)
	{
		return $this->collection[$key];
	}

	/**
	 * Sets the element
	 * @param string $key name of the element
	 * $param mixed $value value of the element
	 * @return Collection
	 */
	function set($key, $value)
	{
		$this->collection[$key] = $value;

		return $this;
	}

	/**
	 * Drops all the elements of the collection
	 *
	 * @return Collection
	 */
	function drop($key)
	{
		unset($this->collection[$key]);

		return $this;
	}

	/**
	 * Gets the names of all elements presented in the collection
	 *
	 * @return array
	 */
	function getKeys()
	{
		return array_keys($this->collection);
	}

	/**
	 * Gets the values of all elements presented in the collection
	 *
	 * @return array
	 */
	function getValues()
	{
		return array_values($this->collection);
	}

	function getIterator()
	{
		return new ArrayIterator($this->collection);
	}

	/**
	 * Merge the collection with the specified
	 *
	 * @param Collection $collection collection to be merged
	 *
	 * @return Collection
	 */
	function merge(Collection $collection)
	{
		$this->append($collection->collection);

		return $this;
	}

	/**
	 * Appends the key=>value associative array to the collection
	 *
	 * @param array $value a key=>value  associative array to be appended
	 *
	 * @return Collection
	 */
	function append(array $values)
	{
		// avoid array_merge wrt set() overridden behaviour
		//$this->collection = array_merge($this->collection, $values);
		foreach ($values as $key => $value) {
			$this->set($key, $value);
		}

		return $this;
	}

	/**
	 * Replaces the existing elements of the collection with a set of new one
	 *
	 * @return Collection
	 */
	function replace(array $values)
	{
		$this->erase()->append($values);

		return $this;
	}

	/**
	 * Drops all the elements presented in a collection
	 *
	 * @return Collection
	 */
	function erase()
	{
		$this->collection = array();

		return $this;
	}

	/**
	 * Presents collection as an array
	 *
	 * @return array
	 */
	function toArray()
	{
		return $this->collection;
	}

	/**
	 * Presents collection as an ArrayObject
	 *
	 * @return ArrayObject
	 */
	function toArrayObject()
	{
		return new ArrayObject($this->collection);
	}

	/**
	 * Copies an object.
	 * If the list of keys is specified then only those keys will be presented in a spawned object
	 * @param array $copy optional limit of keys to be copied to the new Collection
	 * @return Collection
	 */
	function spawn(array $copy = null)
	{
		$spawned = clone $this;

		if (null !== $copy) {
			$spawned->collection = array_intersect_key(
				$this->collection,
				array_combine($copy, $copy)
			);
		}

		return $spawned;
	}
}

?>