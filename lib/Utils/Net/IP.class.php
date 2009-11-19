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
 * @ingroup Utils_Net
 */
final class IP implements IStringCastable
{
	/**
	 * @var integer
	 */
	private $longIP;

	/**
	 * @throws ArgumentException
	 * @return IP
	 */
	static function create($ip)
	{
		return new self($ip);
	}

	/**
	 * @throws ArgumentException
	 */
	function __construct($ip)
	{
		$this->setIp($ip);
	}

	/**
	 * @return string
	 */
	function getIP()
	{
		return long2ip($this->longIP);
	}

	/**
	 * @return boolean
	 */
	function equalTo(IP $ip)
	{
		return $this->getLongIp() == $ip->getLongIp();
	}

	/**
	 * @return integer
	 */
	function getIPAsInteger()
	{
		return $this->longIP;
	}

	function __toString()
	{
		return $this->getIP();
	}

	/**
	 * @throws ArgumentException
	 * @return IP an object itself
	 */
	function setIp($ip)
	{
		if (ip2long($ip) === -1) {
			throw new ArgumentException('ip', 'wrong ip given');
		}

		$this->longIP = ip2long($ip);

		return $this;
	}
}

?>