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
class KeyWrapperCachePeer extends CachePeer
{
	private $marker;

	/**
	 * @var CachePeer
	 */
	private $peer;

	function __construct($marker, CachePeer $peer)
	{
		$this->marker = $marker;
		$this->peer = $peer;
	}

	/**
	 * Returns the value specified by key. If key is not defined, NonexistentCacheKeyException is
	 * thrown
	 * @throws NonexistentCacheKeyException
	 */
	function get($key)
	{
		return $this->peer->get($this->mangleKey($key));
	}

	/**
	 * Returns the key=value hash of found values specified by the list of keys
	 * @return array
	 */
	function getList(array $keys)
	{
		//WOW
		$mappedKeys = array();
		$mangledKeys = array();
		foreach ($keys as $key)
		{
			$mangledKey = $this->mangleKey($key);
			$mappedKeys[$mangledKey] = $key;
			$mangledKeys[] = $mangledKey;
		}

		$fetchedValues = $this->peer->getList($mangledKeys);
		$fetchedDemangledValues = array();
		foreach ($fetchedValues as $fetchedMangledKey => $fetchedValue)
		{
			$fetchedDemangledKey = $mappedKeys[$fetchedMangledKey];
			$fetchedDemangledValues[$fetchedDemangledKey] = $fetchedValue;
		}

		return $fetchedDemangledValues;
	}

	/**
	 * Sets the value
	 * @return CachePeer
	 */
	function set($key, $value, $ttl = CacheTtl::HOUR)
	{
		return $this->peer->set($this->mangleKey($key), $value, $ttl);
	}

	/**
	 * Sets the value only if it is not defined by key inside cache peer
	 * @return CachePeer
	 */
	function add($key, $value, $ttl = CacheTtl::HOUR)
	{
		return $this->peer->add($this->mangleKey($key), $value, $ttl);
	}

	/**
	 * Sets the value only if it is already defined by key
	 * @return CachePeer
	 */
	function replace($key, $value, $ttl = CacheTtl::HOUR)
	{
		return $this->peer->replace($this->mangleKey($key), $value, $ttl);
	}

	/**
	 * Drops the value specified by the key
	 * @return CachePeer
	 */
	function drop($key)
	{
		return $this->peer->drop($this->mangleKey($key));
	}

	private function mangleKey($key)
	{
		return $this->marker . '_' . $key;
	}
}

?>