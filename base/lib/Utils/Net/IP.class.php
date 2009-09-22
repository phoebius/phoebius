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
 * @ingroup Net
 */
final class IP
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

	/**
	 * @return string
	 */
	function toString()
	{
		return $this->getIP();
	}

	function __toString()
	{
		return $this->toString();
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