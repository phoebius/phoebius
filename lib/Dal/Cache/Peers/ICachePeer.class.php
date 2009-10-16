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
 * @ingroup CachePeers
 */
interface ICachePeer
{
	/**
	 * Returns the value by the key. If the specified key is not defined,
	 * NonexistentCacheKeyException is thrown
	 * @throws NonexistentCacheKeyException
	 * @return mixed
	 */
	function get($key);

	/**
	 * Returns the key=value hash of found values specified by the list of keys
	 * @return array
	 */
	function getList(array $keys);

	/**
	 * Sets the value
	 * @param string
	 * @param mixed
	 * @param int|null key lifetime in seconds; null will force key to live forever
	 * @return ICachePeer
	 */
	function set($key, $value, $ttl = null);

	/**
	 * Sets the value if and only if it is not defined by the key inside cache peer
	 * @param string
	 * @param mixed
	 * @param int|null key lifetime in seconds; null will force key to live forever
	 * @return ICachePeer
	 */
	function add($key, $value, $ttl = null);

	/**
	 * Sets the value if and only if it is already defined by the key
	 * @param string
	 * @param mixed
	 * @param int|null key lifetime in seconds; null will force key to live forever
	 * @return ICachePeer
	 */
	function replace($key, $value, $ttl = null);

	/**
	 * Drops the value specified by the key
	 * @return ICachePeer
	 */
	function drop($key);

	/**
	 * Cleans all the storage of cache peer. Should not be used in production environments
	 * @return ICachePeer
	 */
	function clean();

	/**
	 * @return boolean
	 */
	function isAlive();
}

?>