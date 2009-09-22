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
 * Principal session wrapper and handler
 */
final class Session extends LazySingleton
{
	private $area  = 'DefaultSession';
	private $state = false;

	/**
	 * @return Session
	 */
	static function getInstance()
	{
		$me = parent::instance(__CLASS__);
		return $me;
	}

	static function userSupertag($checkIP = true)
	{
		return sha1(self::userSid((string)$checkIP, $checkIP));
	}

	/**
	 * @return Session
	 */
	static function open()
	{
		session_name( self::userSid($this->area) );
		return self::getInstance()->openSession();
	}

	static function close()
	{
		self::getInstance()->closeSession();
	}

	function getArea()
	{
		return $this->area;
	}

	/**
	 * @return Session
	 */
	function setArea($area)
	{
		$this->area = $area;
		$this->state = false;
		session_name( self::userSid($this->area) );

		return $this;
	}

	function getState()
	{
		return $this->state;
	}

	/**
	 * @return Session
	 */
	function openSession()
	{
		if (false == $this->restore())
		{
			return $this->create();
		}

		return $this;
	}

	function closeSession()
	{
		$_SESSION = array();
		session_destroy();
		$this->state = false;
	}

	private function restore()
	{
		if (!$this->state)
		{
			if (isset($_REQUEST[ session_name() ]) )
			{
				session_start();
				$_SESSION[__CLASS__]['latestRequest'] = time();
				if ($_SESSION[__CLASS__]['userHash'] == self::userSid($this->area))
				{
					$this->state = true;
				}
			}
		}

		return $this->state;
	}

	private function create()
	{
		if (!$this->state)
		{
			session_set_cookie_params(0,"/");
			session_start();
			$this->state = true;
			$_SESSION[__CLASS__]['created'] = time();
			$_SESSION[__CLASS__]['latestRequest'] = time();
			$_SESSION[__CLASS__]['userHash'] = self::userSid($this->area);
		}

		return true;
	}

	private static function userSid($uid = null, $secure = true, $length = 40)
	{

		$uq_items = array();

		if ($secure)
		{
			$uq_items = IP::getUserIpList();
		}

		if ($uid)
		{
			$uq_items[] = $uid;
		}

		$uq_fields = array
		(
	        'HTTP_USER_AGENT', 'HTTP_ACCEPT_LANGUAGE', 'HTTP_ACCEPT_CHARSET',
	        'HTTP_ACCEPT_ENCODING', 'HTTP_TE', 'HTTP_UA_CPU', 'HTTP_UA_OS', 'HTTP_UA_COLOR',
	        'HTTP_UA_PIXELS', 'HTTP_UA_VOICE',
		);

		foreach ($uq_fields as $uq_field)
		{
			if (isset($_SERVER[$uq_field]))
			{
				$uq_items[] = $_SERVER[$uq_field];
			}
		}

		$string = join("|", $uq_items);

		if ($length > 40)
		{
			$length = 40;
		}
		return substr(sha1($string), 0, $length);
	}

}

?>