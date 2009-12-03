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
 * PHP type helper utilities
 * @ingroup Utils
 */
final class TypeUtils extends StaticClass
{
	/**
	 * Get string representation of the type
	 *
	 * @param mixed $value
	 * @return string
	 */
	static function getName($value)
	{
		if (is_null($value)) {
			return 'NULL';
		}

		if (is_object($value)) {
			return get_class($value);
		}

		return gettype($value);
	}

	/**
	 * Determines whether the value is integer
	 * @param mixed $value
	 * @return boolean
	 */
	static function isInteger($value)
	{
		return (
			   is_numeric($value)
			&& ($value == (int) $value)
			&& (strlen($value) == strlen((int) $value))
		);
	}

	/**
	 * Determines whether the type (interface of class) exists
	 *
	 * @param string|object $object
	 * @return boolean
	 */
	static function isExists($object)
	{
		$name = self::resolveName($object);

		return
			class_exists($name, true)
			|| interface_exists($name, true);
	}

	/**
	 * Determines whether the type (interface of class) is already defined
	 *
	 * @param string|object $object
	 * @return boolean
	 */
	static function isDefined($object)
	{
		$name = self::resolveName($object);

		return
			class_exists($name, false)
			|| interface_exists($name, false);
	}

	/**
	 * Determines whether class is the child class of the parent class
	 *
	 * @param string|object $child
	 * @param string|object $parent
	 * @return boolean
	 */
	static function isChild($child, $parent)
	{
		$child = self::resolveName($child);
		$parent = self::resolveName($parent);

		Assert::isTrue(
			self::isExists($child),
			'unknown type %s',
			$child
		);

		Assert::isTrue(
			self::isExists($parent),
			'unknown type %s',
			$parent
		);

		return (
			   in_array($parent, class_implements($child))
			|| in_array($parent, class_parents($child))
		);
	}

	private static function resolveName($object)
	{
		return
			is_object($object)
				? get_class($object)
				: $object;
	}
}

?>