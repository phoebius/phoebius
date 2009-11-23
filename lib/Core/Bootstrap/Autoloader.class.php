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
 * Just-in-time cached class autoloaded. Finds a file containing the requested class, loads it and puts
 * to a cache.
 *
 * @ingroup Core_Bootstrap
 */
final class Autoloader extends LazySingleton implements IAutoloader
{
	/**
	 * @var boolean
	 */
	private $isInitialized = false;

	/**
	 * @var string
	 */
	private $mutexFilename;

	/**
	 * @var string
	 */
	private $cacheFilename;

	/**
	 * @var boolean
	 */
	private $cacheFileIsLoaded = false;

	/**
	 * @var array of {@link IClassResolver}
	 */
	private $resolvers = array();

	/**
	 * Already found class paths
	 * @var array
	 */
	private $classPaths = array();

	/**
	 * Used to supress cached in destructor if any class is resolved after cleaning the cache.
	 * This hint is needed to prevent inconsistent caching order
	 * @var boolean
	 */
	private $supressFlush = false;

	/**
	 * @var boolean
	 */
	private $cacheFileModification = false;

	/**
	 * @var string
	 */
	private $slotId = APP_GUID;

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
	function setSlotId($slotId)
	{
		Assert::isScalar($slotId);
		Assert::isFalse($this->isInitialized, 'too late to set slotId: already initialized');

		$this->slotId = $slotId;

		return $this;
	}

	/**
	 * @return string
	 */
	function getSlotId()
	{
		return $this->slotId;
	}

	/**
	 * @return Autoloader
	 */
	function addResolver(IClassResolver $classResolver)
	{
		$this->resolvers[] = $classResolver;

		return $this;
	}

	/**
	 * Initializes the autoloader
	 * @return void
	 */
	private function initialize()
	{
		if ($this->isInitialized) {
			return;
		}

		$this->isInitialized = true;

		$cacheDirectory = PathResolver::getInstance()->getTmpDir($this);
		$this->mutexFilename =
			$cacheDirectory . DIRECTORY_SEPARATOR .
			$this->getMutexId() . '.mutex';

		if (!file_exists($this->mutexFilename)) {
			file_put_contents($this->mutexFilename, null);
		}

		$this->cacheFilename =
			$cacheDirectory . DIRECTORY_SEPARATOR .
			'merged_' . sha1($this->slotId) . '_' . filemtime($this->mutexFilename) . '.php';

		return $this;
	}

	/**
	 * @return string
	 */
	private function getMutexId()
	{
		$ids = array();

		foreach ($this->resolvers as $resolver) {
			$ids[] = $resolver->getId();
		}

		$hash = join("", $ids);

		return sha1($hash);
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
	 * Drops the persistent class cache
	 * @return Autoloader
	 */
	function clearCache()
	{
		$this->initialize();

		try {
			touch($this->mutexFilename);

			//clear the cache
			if ($this->cacheFileIsLoaded) {
				unlink($this->cacheFilename);
			}
		}
		catch (ExecutionContextException $e) {
			//doesn't matter what happened
		}

		$this->cacheFileIsLoaded = true;
		$this->classPaths = array();
		$this->supressFlush = true;

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
		$this->initialize();

		Assert::isScalar($classname);

		if (PhpUtils::typeExists($classname)) {
			return true;
		}

		if (false === $this->cacheFileIsLoaded) {
			try {
				include $this->cacheFilename;
			}
			catch (ExecutionContextException $e) {
				file_put_contents($this->cacheFilename, '<? ');
			}

			$this->cacheFileIsLoaded = true;

			//are you happy? we've got it
			if (PhpUtils::typeExists($classname, false)) {
				return true;
			}
		}

		//only try, nevermind if it fails
		$this->lockCacheFile();

		$found = $this->invokeResolvers($classname, true);

		if (!$found) {
			$found = $this->invokeResolvers($classname, false);
		}

		return $found;
	}

	private function invokeResolvers($classname, $useCacheOnly)
	{
		Assert::isBoolean($useCacheOnly);

		foreach ($this->resolvers as $resolver) {
			$result = $resolver->loadClassFile($classname, $useCacheOnly);
			if ($result && ($classpath = $resolver->getClassPath($classname, true))) {
				$this->classPaths[] = $classpath;
				break;
			}
		}

		return PhpUtils::typeExists($classname);
	}

	/**
	 * Flushes cached data if modified
	 */
	function __destruct()
	{
		if (!$this->supressFlush && !empty($this->classPaths)) {
			$this->flushCache();
		}
	}

	/**
	 * Locks the cache file so it will be used to flush the cache at the end of script execution.
	 * If another script will lock the cache between the time of the first lock and the time of
	 * script execution end, the cache wouldn't be flushed here
	 * @return void
	 */
	private function lockCacheFile()
	{
		if (!$this->cacheFileModification && !$this->supressFlush) {
			clearstatcache();
			$this->cacheFileModification = filemtime($this->cacheFilename);
		}
	}

	/**
	 * Flushes the cache to a locked cache file
	 * @return void
	 */
	private function flushCache()
	{
		//if the file has been modified, do not touch it
		clearstatcache();
		if ($this->cacheFileModification === filemtime($this->cacheFilename)) {
			$prependingContents = '';
			$stripPrefix = false;
			foreach ($this->classPaths as $classpath) {
				//possibly speed ups the file inclusion
				$fileContents = php_strip_whitespace($classpath);
				if ($this->cacheFileIsLoaded || $stripPrefix) { // means that file exists
					$fileContents = preg_replace('/^\<\?(php)?/', '', $fileContents);
				}
				else {
					$stripPrefix = true;
				}

				$prependingContents .= preg_replace('/\?\>$/', '', $fileContents);

			}

			try {
				touch($this->cacheFilename);
			}
			catch (Exception $e) {
				// nothing
			}

			file_put_contents(
				$this->cacheFilename,
				$prependingContents,
				FILE_APPEND	| LOCK_EX
			);
		}
	}
}

?>