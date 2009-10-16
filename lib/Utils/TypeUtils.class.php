<?php
/* ***********************************************************************************************
 *
 * Phoebius Framework
 *
 * **********************************************************************************************
 *
 * Copyright notice
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