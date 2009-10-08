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
final class XCachePeer extends CachePeer
{
	const VAR_PACKED = 0;
	const VAR_OPCODE = 1;

	/**
	 * @return XCachePeer
	 */
	static function create($peerId)
	{
		return new self($peerId);
	}

	/**
	 * @see CachePeer::clean()
	 *
	 * @return XCachePeer
	 */
	function clean()
	{
		Assert::isTrue(
			!ini_get('xcache.admin.enable_auth'),
			'cannot clear the XCache storage due it requires the authentication'
		);

		xcache_clear_cache(XC_TYPE_VAR, 0);

		return $this;
	}

	/**
	 * @see CachePeer::drop()
	 *
	 * @return XCachePeer
	 */
	function drop($key)
	{
		xcache_unset($key);

		return $this;
	}

	/**
	 * @see CachePeer::get()
	 * @throws NonexistentCacheKeyException
	 */
	function get($key)
	{
		if ($this->isCachedKey($key))
		{
			$data = xcache_get($key);
			return $this->unpackData($data);
		}
		else
		{
			throw new NonexistentCacheKeyException($key);
		}
	}

	/**
	 * @see CachePeer::add()
	 * @return XCachePeer
	 */
	function add($key, $value, $ttl = CacheTtl::HOUR)
	{
		if (!$this->isCachedKey($key))
		{
			$this->set($key, $value, $ttl);
		}

		return $this;
	}

	/**
	 * @see CachePeer::replace()
	 *
	 * @return XCachePeer
	 */
	function replace($key, $value, $ttl = CacheTtl::HOUR)
	{
		if ($this->isCachedKey($key))
		{
			$this->set($key, $value, $ttl);
		}

		return $this;
	}

	/**
	 * @see CachePeer::set()
	 *
	 * @return XCachePeer
	 */
	function set($key, $value, $ttl = CacheTtl::HOUR)
	{
		Assert::isNumeric($ttl);

		xcache_set($key, $this->packValue($value), $ttl);

		return $this;
	}

	/**
	 * @see CachePeer::isAlive()
	 *
	 * @return boolean
	 */
	function isAlive()
	{
		return true;
	}

	/**
	 * We override the native packers due XCache system allows us to store variables as-is
	 * @see http://xcache.lighttpd.net/wiki/XcacheApi
	 * @see CachePeer::packValue()
	 */
	protected function packValue($value)
	{

		if (
				   // objects should be packed statically due XCache cannot cache them
				   is_object($value)
				   // if it is an array, and it is callable, it has an object inside and should
				   // be packed manually statically
				|| ( is_array($value) && is_callable($value) && is_object(reset($value)) )
			)
		{
			$type = self::VAR_PACKED;
			$value = parent::packValue($value);
		}
		else
		{
			$type = self::VAR_OPCODE;
			//do not pack this value
		}

		return array(
			$type, $value
		);
	}

	/**
	 * @see CachePeer::unpackData()
	 */
	protected function unpackData($data)
	{
		$var = null;

		do
		{
			if (!is_array($data))
			{
				break;
			}

			list($type, $var) = $data;

			if ($type == self::VAR_PACKED)
			{
				$var = parent::unpackData($var);
			}

		} while (false);

		return $var;
	}

	private function isCachedKey($key)
	{
		return xcache_isset($key);
	}

}

?>