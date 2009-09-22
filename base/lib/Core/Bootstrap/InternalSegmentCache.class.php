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
 * Implements the internal cacher to optimize bootstrapping procedures. It uses eaccelerator,
 * xcache and FS storage. If some of your classes/singletons are needed a session data between
 * every script execution, just inherit this cacher and use the protected API
 * @ingroup Bootstrap
 */
abstract class InternalSegmentCache
{
	/**
	 * Data itself
	 * @var array
	 */
	private $cache = array();

	/**
	 * @var boolean
	 */
	private $cacheIsRead = false;

	/**
	 * Flush the cache after all?
	 */
	private $flush = false;

	/**
	 * Gets the unique identifier of the class that needed the cache
	 * @return scalar
	 */
	abstract protected function getCacheId();

	/**
	 * Determines whether the value is in the cache
	 * @param scalar $key
	 * @return boolean
	 */
	final protected function isCached($key)
	{
		Assert::isScalar($key);

		if (!$this->cacheIsRead) {
			$this->readCache();
			$this->cacheIsRead = true;
		}

		return isset($this->cache[$key]);
	}

	/**
	 * Puts the value into the cache
	 * @param scalar $key
	 * @param mixed $value
	 * @return InternalSegmentCache
	 */
	final protected function cache($key, $value)
	{
		Assert::isScalar($key);

		//hack to read the cache and initialze an array
		$this->isCached($key);

		$this->cache[$key] = $value;
		$this->flush = true;

		return $this;
	}

	/**
	 * Gets the value (identified by the specified key) from the cache
	 * @param scalar $key
	 * @return mixed the value identified by the specified key
	 */
	final protected function getCached($key)
	{
		Assert::isScalar($key);

		Assert::isTrue(
			$this->isCached($key),
			'Call the InternalSegmentCache::isCached() first! No data found in cache :/'
		);

		return $this->cache[$key];
	}

	/**
	 * Drops the value identified by the specified key
	 * @param scalar $key
	 * @return InternalSegmentCache
	 */
	final protected function uncache($key)
	{
		Assert::isScalar($key);

		if ($this->isCached($key)) {
			unset($this->cache[$key]);
			$this->flush = true;
		}

		return $this;
	}

	/**
	 * Drops the overall cache
	 * @return InternalSegmentCache
	 */
	final protected function dropCache()
	{
		$this->cache = array();
		$this->cacheIsRead = true;
		$this->flush = true;

		return $this;
	}

	/**
	 * @return string
	 */
	private function packData(array $data)
	{
		return serialize($data);
	}

	/**
	 * @return array
	 */
	private function unpackData($data)
	{
		return (array) unserialize($data);
	}

	/**
	 * Actually, is is not the OOP-style to use similar methods in one, and we can divide them
	 * by different classes, but:
	 * 1. internal segmentation works at bootstrap, when we cannot autoload the requested classes
	 * 2. segmentation should work as fast as it can
	 * @return InternalSegmentCache
	 */
	private function readCache()
	{
		$cache = null;

		if (function_exists('eaccelerator_get')) {
			$cache = eaccelerator_get($this->getCacheId());
		}
		else if (extension_loaded('xcache')) {
			$id = $this->getCacheId();
			if (xcache_isset($id)) {
				$cache = xcache_get($id);
			}
		}
		else {
			try {
				//Read the cache
				$file = $this->getCacheFilename();
				$cache = $this->unpackData(file_get_contents($file));
			}
			catch (ExecutionContextException $e) {
				//nothing
			}
		}

		if (is_array($cache)) {
			$this->cache = $cache;
		}
		else {
			$this->dropCache();
		}

		return $this;
	}

	/**
	 * @return InternalSegmentCache
	 */
	private function flushCache()
	{
		if (function_exists('eaccelerator_set')) {
			eaccelerator_set($this->getCacheId(), $this->cache);
		}
		else if (extension_loaded('xcache')) {
			$id = $this->getCacheId();
			xcache_set($id, $this->cache);
		}
		else {
			$file = $this->getCacheFilename();
			$data = $this->packData($this->cache);
			file_put_contents($file, $data);
		}

		return $this;
	}

	/**
	 * @return string
	 */
	private function getCacheFilename()
	{
		return PathResolver::getInstance()->getTmpDir($this)
			. DIRECTORY_SEPARATOR
			. $this->getCacheId();
	}

	/**
	 * Flushes the cache at the script shutdown
	 */
	final function __destruct()
	{
		if ($this->flush) {
			$this->flushCache();
		}
	}
}

?>