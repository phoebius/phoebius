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
	 * @return boolean
	 */
	static function isBoolean($value)
	{
		return (
			   is_bool($value)
			|| in_array($value, array(0, 1, 'f', 't', 'false', 'true'))
		);
	}

	/**
	 * @return boolean
	 */
	static function isInherits($class, $type)
	{
		return (
			   in_array($type, class_implements($class))
			|| in_array($type, class_parents($class))
		);
	}
}

?>