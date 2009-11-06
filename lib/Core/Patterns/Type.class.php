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
	 * @return Type
	 */
	static function of($object)
	{
		return new self ($object);
	}

	/**
	 * @throws ArgumentException
	 * @param string $name
	 */
	function __construct($object)
	{
		$name = is_object($object)
			? get_class($object)
			: $object;

		if (
				!class_exists($name, true)
				&& !interface_exists($name, true)
		) {
			Assert::isUnreachable('unknown type %s', $name);
		}

		$this->name = $name;
	}

	/**
	 * @return boolean
	 */
	function isChildOf(Type $parent)
	{
		return
			   is_subclass_of($this->name, $parent->name)
			|| in_array($parent->name, class_implements($this->name));
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