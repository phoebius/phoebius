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
 * echo $dict[HttpRequestDictionary::REFERER];
 * @endcode
 *
 * @ingroup Core_Patterns
 */
abstract class Dictionary implements ArrayAccess
{
	/**
	 * @var array
	 */
	private $get = array();

	/**
	 * @param array $values values to be set to the dictionary
	 */
	final function __construct(array $values)
	{
		$this->readFields();
		$this->import($values);
	}

	/**
	 * Gets the field value
	 *
	 * @param string $name name of the field
	 * @return mixed value of the field
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
	 * Gets the name=>value associative array of fields
	 *
	 * @return array
	 */
	function getFields()
	{
		return $this->fields;
	}

	/**
	 * Gets the list of field names
	 *
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
	 * Default values to be set to dictionary fields in case when fields are not presented
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

	/**
	 * @return Dictionary itself
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
}

?>