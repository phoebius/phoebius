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
 * @ingroup Cache
 */
abstract class CachePeer
{
	/**
	 * Returns the value specified by key. If key is not defined, NonexistentCacheKeyException is
	 * thrown
	 * @throws NonexistentCacheKeyException
	 */
	abstract function get($key);

	/**
	 * Returns the key=value hash of found values specified by the list of keys
	 * @return array
	 */
	abstract function getList(array $keys);

	/**
	 * Sets the value
	 * @return CachePeer
	 */
	abstract function set($key, $value, $ttl = CacheTtl::HOUR);

	/**
	 * Sets the value only if it is not defined by key inside cache peer
	 * @return CachePeer
	 */
	abstract function add($key, $value, $ttl = CacheTtl::HOUR);

	/**
	 * Sets the value only if it is already defined by key
	 * @return CachePeer
	 */
	abstract function replace($key, $value, $ttl = CacheTtl::HOUR);

	/**
	 * Drops the value specified by the key
	 * @return CachePeer
	 */
	abstract function drop($key);

	/**
	 * Cleans all the storage of cache peer. Should not be used in production environments
	 * @return CachePeer
	 */
	abstract function clean();

	/**
	 * @return boolean
	 */
	abstract function isAlive();

	/**
	 * Helper method used to pack non-scalar values into strings that could be stored inside
	 * almost any cache peer
	 * @return string
	 */
	protected function packValue($value)
	{
		Assert::isFalse(
			is_resource($value),
			'resources are not cacheable'
		);

		$data = serialize($value);
		return $data;
	}

	/**
	 * Helper method to unpack previously packed value
	 * @return mixed
	 */
	protected function unpackData($data)
	{
		return unserialize($data);
	}

}

?>