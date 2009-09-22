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
class RuntimeCachePeer extends CachePeer
{
	private $cache = array();

	/**
	 * @return RuntimeCachePeer
	 */
	static function create()
	{
		return new self();
	}

	/**
	 * @see CachePeer::add()
	 * @return RuntimeCachePeer
	 */
	function add($key, $value, $ttl = CacheTtl::HOUR)
	{
		if (!isset($this->cache[$key]))
		{
			$this->cache[$key] = $value;
		}

		return $this;
	}

	/**
	 * @see CachePeer::clean()
	 * @return RuntimeCachePeer
	 */
	function clean()
	{
		$this->cache = array();

		return $this;
	}

	/**
	 * @see CachePeer::drop()
	 * @return RuntimeCachePeer
	 */
	function drop($key)
	{
		unset($this->cache[$key]);
		return $this;
	}

	/**
	 * @see CachePeer::get()
	 * @throws NonexistentCacheKeyException
	 */
	function get($key)
	{
		if (isset($this->cache[$key]))
		{
			return $this->cache[$key];
		}
		else
		{
			throw new NonexistentCacheKeyException($key);
		}
	}

	/**
	 * @see CachePeer::replace()
	 * @return RuntimeCachePeer
	 */
	function replace($key, $value, $ttl = CacheTtl::HOUR)
	{
		if (isset($this->cache[$key]))
		{
			$this->cache[$key] = $value;
		}

		return $this;
	}

	/**
	 * @see CachePeer::set()
	 * @return RuntimeCachePeer
	 */
	function set($key, $value, $ttl = CacheTtl::HOUR)
	{
		$this->cache[$key] = $value;
		return $this;
	}

	/**
	 * @return array
	 */
	protected function getCache()
	{
		return $this->cache;
	}

}

?>