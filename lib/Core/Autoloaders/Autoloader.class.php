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
 * Just-in-time cached class autoloaded. Finds a file containing the requested class, loads it and puts
 * to a cache.
 *
 * @ingroup Core_Bootstrap
 */
final class Autoloader extends LazySingleton
{
	private $pathCacher;
	private $isRegistered = false;

	/**
	 * @var array of {@link IClassResolver}
	 */
	private $resolvers = array();

	/**
	 * @return Autoloader
	 */
	static function getInstance()
	{
		return LazySingleton::instance(__CLASS__);
	}

	/**
	 * @return Autoloader
	 */
	function addResolver(IClassResolver $classResolver)
	{
		if (!$this->isRegistered)
			$this->register();
		
		$this->resolvers[] = $classResolver;

		return $this;
	}

	function register()
	{
		spl_autoload_register(array($this, 'loadClass'));

		return $this;
	}

	function unregister()
	{
		spl_autoload_unregister(array($this, 'loadClass'));

		return $this;
	}

	/**
	 * Searchs for the class by it's name using the added resolvers, loads it in scope of itself
	 * and adds it to class cache
	 * @param string $classname
	 * @return boolean shows whether the class was successfully loaded or not
	 */
	function loadClass($classname)
	{
		Assert::isScalar($classname);

		if (TypeUtils::isDefined($classname)) {
			return true;
		}

		foreach ($this->resolvers as $resolver) {
			$result = $resolver->getClassPath($classname);
			if ($result) {
				try {
					require $result;
					
					return true;
				}
				catch (Exception $e) {
					$message = sprintf(
						'Exception thrown when autoloading %s from %s:%s',
						$result, $e->getFile(), $e->getLine()
					);
					
					trigger_error($message, E_USER_ERROR);
				}
			}
		}
	}
}
