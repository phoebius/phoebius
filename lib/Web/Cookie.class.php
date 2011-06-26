<?php
/* ***********************************************************************************************
 *
 * Phoebius Framework
 *
 * **********************************************************************************************
 *
 * Copyright (c) 2011 Scand Ltd.
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
 * Represents an HTTP cookie
 *
 * @ingroup App_Web
 */
final class Cookie
{
	const RFC_MAX_SIZE = 4096;
	
	private $name;
	private $value;
	private $ttl = 0;
	private $path = "/";
	
	/**
	 * Initializes a cookie
	 * @param string $name
	 * @param string $value
	 * @param int $ttl
	 * @param string $path
	 */
	function __construct($name, $value, $ttl = 0, $path = "/")
	{
		$this->name = $name;
		$this->setValue($value);
		$this->setTtl($ttl);
		$this->setPath($path);
	}
	
	/**
	 * Gets the name of a cookie
	 */
	function getName()
	{
		return $this->name;
	}
	
	/**
	 * Gets the value of a cookie
	 */
	function getValue()
	{
		return $this->value;
	}
	
	/**
	 * Sets the cookie value. It cannot be longer than Cookie::RFC_MAX_SIZE.
	 * @param string $value
	 * @return Cookie
	 */
	function setValue($value)
	{
		Assert::isScalar($value);
		Assert::isTrue(strlen($value) < self::RFC_MAX_SIZE);
		
		$this->value = $value;
		
		return $this;
	}
	
	/**
	 * Sets a cookie lifetime (in seconds)
	 * @param int $ttl
	 * @return Cookie
	 */
	function setTtl($ttl)
	{
		Assert::isPositiveInteger($ttl);
		
		$this->ttl = $ttl;
		
		return $this;
	}
	
	/**
	 * Gets a cookie lifetime (in seconds)
	 * @return int
	 */
	function getTtl()
	{
		return $this->ttl;
	}
	
	/**
	 * Gets a cookie expiration time (in unix timestamp)
	 * @return int
	 */
	function getExpire()
	{
		return 
			$this->ttl 
				? time() + $this->ttl 
				: 0;
	}
	
	/**
	 * Gets a cookie path
	 * @return string
	 */
	function getPath()
	{
		return $this->path;
	}
	
	/**
	 * Sets the cookie path
	 * @param string $path
	 * @return Cookie
	 */
	function setPath($path)
	{
		Assert::isScalar($path);
		
		$this->path = $path;
		
		return $this;
	}
}

?>