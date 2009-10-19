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
 * Represents a C#-like type. Type is a class or an interface defiend in PHP scope
 * @ingroup Core_Patterns
 */
class Type
{
	/**
	 * @var string
	 */
	private $name;

	/**
	 * @throws ArgumentException
	 * @param string $typename
	 * @return Type
	 */
	static function create($typename)
	{
		return new self ($typename);
	}

	/**
	 * @param string $typename
	 * @return Type
	 */
	static function check($typename)
	{
		try {
			return new self ($typename);
		}
		catch (ArgumentException $e) {
			Assert::isFalse(true, 'unknown type');
		}
	}

	/**
	 * @throws ArgumentException
	 * @return Type
	 */
	static function typeof($object)
	{
		return new self (
			is_object($object)
				? get_class($object)
				: $object
		);
	}

	/**
	 * @throws ArgumentException
	 * @param string $typename
	 */
	function __construct($typename)
	{
		$typename = is_object($typename)
			? get_class($typename)
			: $typename;

		if (!class_exists($typename, true) && !interface_exists($typename, true))
		{
			throw new ArgumentException('typename', 'unknown type ' . $typename);
		}

		$this->name = $typename;
	}

	/**
	 * @return boolean
	 */
	function isDescendantOf(Type $typename)
	{
		return
			   is_subclass_of($this->name, $typename->name)
			|| in_array($typename->name, class_implements($this->name));
	}

	/**
	 * @return string
	 */
	function getName()
	{
		return $this->name;
	}
}

?>