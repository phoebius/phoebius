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
 * Controller for lazy singletons, version 3
 * @ingroup Patterns
 */
abstract class LazySingleton implements ISingleton
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
	 * @param string $className classname
	 * @return LazySingleton object itself
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