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
 * @ingroup Web
 */
class WebRequestPart extends Enumeration
{
	const GET = 'GET';
	const POST = 'POST';
	const FILES	= 'FILES';
	const COOKIE = 'COOKIE';
//	const SESSION = 'SESSION';
//	const ATTACHED;
//	const SERVER = 'SERVER';

	/**
	 * @return WebRequestPart
	 */
	static function get()
	{
		return new self (self::GET);
	}

	/**
	 * @return WebRequestPart
	 */
	static function post()
	{
		return new self (self::POST);
	}

	/**
	 * @return WebRequestPart
	 */
	static function files()
	{
		return new self (self::FILES);
	}

	/**
	 * @return WebRequestPart
	 */
	static function cookie()
	{
		return new self (self::COOKIE);
	}
}

?>