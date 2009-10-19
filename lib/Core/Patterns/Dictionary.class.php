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
 * Represents a strict key=>value collection used for flexible mock-objects instantiation
 * @ingroup Core_Patterns
 */
abstract class Dictionary
{
	/**
	 * @var array
	 */
	private $fields = array();

	/**
	 * @param array $values
	 * @param boolean $supressMissing
	 */
	final function __construct(array $values, $supressMissing = true)
	{
		$this->readFields();
		$this->import($values, $supressMissing);
	}

	/**
	 * @return Dictionary an object itself
	 */
	function import(array $values, $supressMissing = false)
	{
		Assert::isBoolean($supressMissing);

		$defaultValues = $this->getDefaultValues();

		foreach ($this->fields as $name => $value) {
			// use array_key_exists here instead of isset
			// because initial values can be null
			if (!array_key_exists($name, $values)) {
				if (array_key_exists($name, $defaultValues)) {
					$values[$name] = $defaultValues[$name];
				}
				else {
					Assert::isTrue($supressMissing, '%s not defined', $name);

					$values[$name] = $value;
				}
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
		Assert::hasIndex($this->fields, $name);

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
	 * @return Dictionary
	 */
	function spawn(Dictionary $parent)
	{
		$me = get_class($this);
		return new $me(
			array_merge(
				$this->getFields(), $parent->getFields()
			)
		);
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