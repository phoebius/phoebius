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
 * Represents a strict collection of predefined named fields
 *
 * Field names are imported from the child's class constants.
 *
 * Example:
 * @code
 * HttpRequestDictionary extends Dictionary
 * {
 * 	const URL = 'HTTP_URL';
 * 	const REFERER = 'HTTP_REFERER';
 * }
 *
 * $dict = new HttpRequestDictionary($_SERVER);
 * echo $dict[HttpRequestDictionary::URL];
 *
 * @endcode
 *
 * @ingroup Core_Patterns
 */
abstract class Dictionary implements ArrayAccess
{
	/**
	 * @var array
	 */
	private $fields = array();

	/**
	 * @param array $values
	 * @param boolean $supressMissing
	 */
	final function __construct(array $values)
	{
		$this->readFields();
		$this->import($values);
	}

	/**
	 * @return Dictionary an object itself
	 */
	private function import(array $values)
	{
		$defaultValues = $this->getDefaultValues();

		foreach (array_keys($this->fields) as $name) {
			if (!isset($values[$name])) {
				$values[$name] =
					isset($defaultValues[$name])
						? $defaultValues[$name]
						: null;
			}

			$this->fields[$name] = $values[$name];
		}

		return $this;
	}

	/**
	 * @return mixed
	 */
	function getField($name)
	{
		Assert::hasIndex(
			$this->fields, $name,
			'unknown field %s for %s',
			$name, get_class($this)
		);

		return $this->fields[$name];
	}

	/**
	 * @return array
	 */
	function getFields()
	{
		return $this->fields;
	}

	/**
	 * @return array
	 */
	function getFieldNames()
	{
		return array_keys($this->fields);
	}

	/**
	 * Defines an interface for easy access to dictionary fields
	 *
	 * @return boolean
	 */
	function offsetExists($offset)
	{
		return array_key_exists($this->fields[$offset]);
	}

	/**
	 * Defines an interface for easy access to dictionary fields
	 *
	 * @return mixed
	 */
	function offsetGet($offset)
	{
		return $this->getField($offset);
	}

	/**
	 * Not implemented, and won't be.
	 *
	 * @return void
	 */
	function offsetSet($offset, $value)
	{
		Assert::isUnreachable('dictionary fields are read-only');
	}

	/**
	 * Not implemented, and won't be.
	 *
	 * @return void
	 */
	function offsetUnset($offset)
	{
		Assert::isUnreachable('dictionary fields are read-only');
	}

	/**
	 * Overridden
	 * @return array
	 */
	protected function getDefaultValues()
	{
		return array ();
	}

	/**
	 * @return void
	 */
	private function readFields()
	{
		$enumerationClass = new ReflectionClass(get_class($this));

		foreach ($enumerationClass->getConstants() as $name) {
			$this->fields[$name] = null;
		}
	}
}

?>