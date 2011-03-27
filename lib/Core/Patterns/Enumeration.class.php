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
 * Strictly-typed immutable enumeration implementation.
 *
 * If you need to create an enumeration you can strictly refer to, just extend this base
 * class and define the consts' in a descendant.
 *
 * Example:
 * @code
 * class Color extends Enumeration
 * {
 * 	const RED = 'red';
 * 	const ORANGE = 'orange';
 * 	const BLACK = 'black';
 * }
 *
 *
 * // usage:
 *
 * function setColor(Color $color)
 * {
 * 	// do smth
 * }
 *
 * $color = new Color(Color::RED);
 * setColor($color);
 *
 * // compilation failure:
 *
 * setColor("red");
 * setColor(Color::RED);
 *
 * @endcode
 *
 * Enumeration::$id is the reflected name of the constant defined within the child class.
 *
 * Enumeration::$value is the value of the constant.
 *
 * @ingroup Core_Patterns
 */
abstract class Enumeration implements IStringCastable
{
	/**
	 * Reflection cache for all enumeration implementations. A value=>key swapped hash
	 * @var array
	 */
	static private $enumerationMembers = array();

	/**
	 * @var string
	 */
	private $id;

	/**
	 * @var scalar
	 */
	private $value;

	/**
	 * @param scalar $value one of the final class constants specified
	 */
	function __construct($value)
	{
		$this->setValue($value);
	}

	/**
	 * Returns the identifier of the enumeration
	 * @return string
	 */
	function getId()
	{
		return $this->id;
	}

	/**
	 * Returns the value for the corresponding identifier
	 * @return scalar
	 */
	function getValue()
	{
		return $this->value;
	}

	/**
	 * Sets the new enumeration value. Value is one the constants defined inside enumeration
	 * @param scalar $value one of the final class constants specified
	 * @return Enumeration itself
	 */
	protected function setValue($value)
	{
		$id = $this->getIdByValue($value);

		if (!$id) {
			Assert::isNotNull(
				$id,
				'unknown value given for %s: `%s`',
				get_class($this),
				$value
			);
		}

		$this->id = $id;
		$this->value = $value;

		return $this;
	}

	/**
	 * Compares the values of two enumeration instances and return true if they are equal,
	 * otherwise false
	 * @param Enumeration $object enumeration to compare to
	 * @return boolean
	 */
	function equals(Enumeration $object)
	{
		return
			get_class($this) == get_class($object)
			&& $this->id == $object->id;
	}

	/**
	 * Clones the enumeration with the same id inside
	 *
	 * @return Enumeration
	 */
	function spawn($id)
	{
		$me = get_class($this);

		return new $me ($id);
	}

	/**
	 * Compares the value of enumeration instance and the value of the specified enumeration
	 * member, and returns true if they are equal, otherwise false
	 * @param scalar $value one of the final class constants specified
	 *
	 * @return boolean
	 */
	function is($value)
	{
		Assert::isTrue(
			false !== $this->getIdByValue($value),
			'unknown value %s for %s',
			$value, get_class($this)
		);

		return $this->value == $value;
	}

	/**
	 * Compares the value of enumeration instance and the value of the specified enumeration
	 * member, and returns true if they are not equal, otherwise false
	 * @param scalar $value one of the final class constants specified
	 * @return boolean
	 */
	function isNot($value)
	{
		return !$this->is($value);
	}

	function __sleep()
	{
		return array("\0".__CLASS__."\0value");
	}

	function __wakeup()
	{
		$this->setValue($this->value);
	}

	function __toString()
	{
		return $this->value;
	}

	/**
	 * Overridden. Returns the id=>value hash of enumeration members
	 * @return array
	 */
	protected function getMembers()
	{
		$enumerationClass = new ReflectionClass(get_class($this));

		$constants = $enumerationClass->getConstants();

		$this->checkMemberIndependency($constants);

		return $constants;
	}

	/**
	 * @return string|null
	 */
	private function getIdByValue($value)
	{
		$enumerationName = get_class($this);

		if (is_object($value)) {
			Assert::isTrue($value instanceof $enumerationName);
		}

		if (!isset (self::$enumerationMembers[$enumerationName])) {
			$members = $this->getMembers();
			self::$enumerationMembers[$enumerationName] = array_flip($members);
		}

		return isset(self::$enumerationMembers[$enumerationName][$value])
			? self::$enumerationMembers[$enumerationName][$value]
			: null;

	}

	/**
	 * @return void
	 */
	private function checkMemberIndependency(array $members)
	{
		$thrashed = array_unique($members);
		if (sizeof($thrashed) != sizeof($members)) {
			$duplicates = $this->getDuplicates($members);

			Assert::isNotEmpty($duplicates, 'core error: duplicates not found');
			Assert::isUnreachable(
				'%s %s enumeration constants has the same value %s',
				join($duplicates, ', '),
				get_class($this),
				$members[reset($duplicates)]
			);
		}
	}

	/**
	 * @return array
	 */
	private function getDuplicates(array $members)
	{
		$yield = array();

		foreach ($members as $comparableMember => $comparableValue) {
			foreach ($members as $member => $value) {
				if ($comparableValue == $value) {
					if (empty($yield)) {
						$yield[] = $comparableMember;
					}

					$yield[] = $member;
				}
			}

			if (!empty($yield)) {
				break;
			}
		}

		return $yield;
	}
}

?>