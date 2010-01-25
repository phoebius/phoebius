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
 * Represents an IP address
 *
 * @ingroup Utils_Net
 */
final class IP implements IStringCastable
{
	/**
	 * @var integer
	 */
	private $int;

	/**
	 * @param string|int
	 * @throws ArgumentException thrown if the specified IP is wrong
	 */
	function __construct($ip)
	{
		if (ip2long($ip) === -1) {
			throw new ArgumentException('ip', 'wrong ip given');
		}

		$this->int = ip2long($ip);
	}

	/**
	 * Determines whether two IP objects are equal
	 * @return boolean
	 */
	function equals(IP $ip)
	{
		return $this->int == $ip->int;
	}

	/**
	 * Gets the long integer representation of the object
	 *
	 * @return integer
	 */
	function toInt()
	{
		return $this->int;
	}

	/**
	 * Gets the texutal representation of the object
	 *
	 * @return string
	 */
	function toString()
	{
		return long2ip($this->int);
	}

	function __toString()
	{
		return $this->toString();
	}
}

?>