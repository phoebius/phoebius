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
 * Controller for lazy singletons, version 3
 * @ingroup Core_Patterns
 */
abstract class LazySingleton
{
	private static $objectInstances = array();

	final protected function __construct()
	{
		//nothing
	}

	final private function __clone()
	{
		//nothing here
	}

	final private function __sleep()
	{
		//nothing here
	}

	/**
	 * Determines whether the singleton class was instantiated
	 * @param string $className classname
	 * @return boolean
	 */
	protected static function isInstantiated($className)
	{
		return isset (self::$objectInstances[$className]);
	}

	/**
	 * Returns the single instance of the singleton (which is created at first request)
	 * @param string classname
	 * @return object
	 */
	protected static function instance($className)
	{
		if (!self::IsInstantiated($className)) {
			self::$objectInstances[$className] = new $className;
		}

		return self::$objectInstances[$className];
	}
}

?>