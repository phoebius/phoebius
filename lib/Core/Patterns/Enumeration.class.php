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
 * Core primitive: basic enumeration implementation
 * @ingroup Core_Patterns
 */
abstract class Enumeration implements IScalarMappable
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
	 * Returns the value for the corresponding identifier set inside this enumeration
	 * @return scalar
	 */
	function getValue()
	{
		return $this->value;
	}

	/**
	 * Sets the new enumeration value. Value is one the constants defined inside enumeration
	 * @return Enumeration
	 */
	function setValue($value)
	{
		$id = $this->getIdByValue($value);

		if (!$id) {
			Assert::isNotNull(
				$id,
				'unknown value given for %s: %s',
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
	 * @return boolean
	 */
	function isEqual(Enumeration $toBeCompared)
	{
		Assert::isTrue(
			get_class($this) == get_class($toBeCompared),
			'different enumeration types are given'
		);

		return $this->id === $toBeCompared->id;
	}

	/**
	 * Compares the values of two enumeration instances and returns true if they are not equal,
	 * otherwise false
	 * @return boolean
	 */
	function isNotEqual(Enumeration $toBeCompared)
	{
		return !$this->equal($toBeCompared);
	}

	/**
	 * @return Enumeration
	 */
	function spawn($id)
	{
		$me = get_class($this);
		return new $me($id);
	}

	/**
	 * Compares the value of enumeration instance and the value of the specified enumeration
	 * member, and returns true if they are equal, otherwise false
	 * @return boolean
	 */
	function isIdentifiedBy($value)
	{
		Assert::isTrue(false !== $this->getIdByValue($value), 'no such value defined');

		return $this->value === $value;
	}

	/**
	 * Compares the value of enumeration instance and the value of the specified enumeration
	 * member, and returns true if they are not equal, otherwise false
	 * @return boolean
	 */
	function isNotIdentifiedBy($value)
	{
		return !$this->isIdentifiedBy($value);
	}

	/**
	 * Compares the value of enumeration instance and the value of the specified enumeration
	 * member, and returns true if they are equal, otherwise false. Alias for
	 * {@link Enumeration::isIdentifiedBy()}
	 * @see Enumeration::isIdentifiedBy()
	 * @return boolean
	 */
	function is($value)
	{
		return $this->isIdentifiedBy($value);
	}

	/**
	 * Compares the value of enumeration instance and the value of the specified enumeration
	 * member, and returns true if they are not equal, otherwise false. Alias for
	 * {@link Enumeration::isNotIdentifiedBy()}
	 * @see Enumeration::isNotIdentifiedBy()
	 * @return boolean
	 */
	function isNot($value)
	{
		return $this->isNotIdentifiedBy($value);
	}

	function __sleep()
	{
		return array("\0".__CLASS__."\0value");
	}

	function __wakeup()
	{
		$this->setValue($this->value);
	}

	/**
	 * @return string
	 */
	function __toString()
	{
		return $this->toString();
	}

	/**
	 * @return string
	 */
	function toString()
	{
		return $this->getValue();
	}

	/**
	 * @return string
	 */
	function toScalar()
	{
		return $this->toString();
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
				if ($comparableValue === $value) {
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