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
 * Encapsulates the request invoked over HTTP
 *
 * @ingroup App_Web
 */
class WebRequest implements ArrayAccess
{
	private static $row = array
	(
		'HTTP_X_FORWARDED_FOR', 'CLIENT_IP', 'HTTP_FROM', 'HTTP_CLIENT_IP', 'HTTP_CLIENTIP',
		'HTTP_CLIENT', 'HTTP_X_FORWARDED', 'HTTP_X_DELEGATE_REMOTE_HOST', 'HTTP_SP_HOST',
	);
	
	private $vars = array(
		WebRequestPart::GET => array(),
		WebRequestPart::POST => array(),
		WebRequestPart::COOKIE => array(),
		WebRequestPart::FILES => array(),
	);

	private $allVars = array();

	/**
	 * @var SiteUrl
	 */
	private $httpUrl;

	/**
	 * @var array
	 */
	private $serverVars;
	private $envVars;
	
	/**
	 * @var WebResponse
	 */
	private $response;

	function __construct(
				array $getVars,
				array $postVars,
				array $cookieVars,
				array $filesVars,
				array $serverVars,
				array $envVars,
				$baseHost = null, 
				$baseUri = '/'
		)
	{
		Assert::isScalar($baseUri);

		$this->vars = array(
			WebRequestPart::GET => $getVars,
			WebRequestPart::POST => $postVars,
			WebRequestPart::COOKIE => $cookieVars,
			WebRequestPart::FILES => $filesVars,
		);

		// GPCF
		$this->allVars = array_replace_recursive($cookieVars, $getVars, $postVars, $filesVars);

		$this->serverVars = $serverVars;
		$this->envVars = $envVars;
		
		$this->httpUrl = SiteUrl::import(
			$this->isSecured() ? 'https' : 'http',
			$this->serverVars['HTTP_HOST'],
			$this->serverVars['SERVER_PORT'],
			$this->serverVars['REQUEST_URI'],
			$baseHost, $baseUri
		);
	}
	
	/**
	 * @return IWebResponse
	 */
	function getResponse()
	{
		if (!$this->response)
			$this->response = new WebResponse($this);

		return $this->response;
	}

	function __clone()
	{
		$this->httpUrl = clone $this->httpUrl;
	}

	/**
	 * Gets the request method.
	 *
	 * @return RequestMethod
	 */
	function getRequestMethod()
	{
		return new RequestMethod($this->serverVars['REQUEST_METHOD']);
	}

	/**
	 * Gets the HTTP protocol
	 *
	 * @return string eigher HTTP/1.0 or HTTP/1.1
	 */
	function getProtocol()
	{
		return $this->serverVars['SERVER_PROTOCOL'];
	}

	/**
	 * Gets the HTTP referer, if any
	 *
	 * @return HttpUrl|null
	 */
	function getHttpReferer()
	{
		if (isset($this->serverVars['HTTP_REFERER'])) {
			try {
				return new HttpUrl($this->serverVars['HTTP_REFERER']);
			}
			catch (Exception $e){}
		}
	}

	/**
	 * Specifies whether request is secured and passed over HTTPS protocol.
	 *
	 * @return boolean
	 */
	function isSecured()
	{
		return isset($this->serverVars['HTTPS']) && !!$this->serverVars['HTTPS'];
	}

	/**
	 * Gets the request URL
	 *
	 * @return SiteUrl|null
	 */
	function getHttpUrl()
	{
		return $this->httpUrl;
	}

	/**
	 * Gets the set of variables passed via the query string
	 *
	 * @return array
	 */
	function getGetVars()
	{
		return $this->getVars;
	}

	/**
	 * Gets the set of variables passed via POST part of the request
	 *
	 * @return array
	 */
	function getPostVars()
	{
		return $this->postVars;
	}

	/**
	 * Gets the set of variables that passed via cookies
	 *
	 * @return array
	 */
	function getCookieVars()
	{
		return $this->cookieVars;
	}

	/**
	 * Gets the set of variables that described as incoming files
	 *
	 * @return array
	 */
	function getFilesVars()
	{
		return $this->filesVars;
	}

	/**
	 * Determines whether variable is set in any of the request part
	 *
	 * @param string $variableName name of the variable to be retreived from the request
	 * @param WebRequestPart $part optional specification of request scope where variable should
	 * 			be looked up
	 *
	 * @return boolean
	 */
	function hasVar($variableName, WebRequestPart $part = null)
	{
		$vars =
			$part
				? $this->vars[$part->getValue()]
				: $this->allVars;

		return isset($vars[$variableName]);
	}

	/**
	 * Gets the variable from the specified request part
	 *
	 * @throws ArgumentException if such variable does not exist
	 * @param string $variableName name of the variable to be retreived from the request
	 * @param WebRequestPart $part optional specification of request scope where variable should
	 * 			be looked up
	 * @return scalar
	 */
	function getVar($variableName, WebRequestPart $part = null)
	{
		$vars =
			$part
				? $this->vars[$part->getValue()]
				: $this->allVars;

		if (isset($variableName, $vars)) {
			return $vars[$variableName];
		}

		throw new ArgumentException('variableName', 'argument is not defined');
	}

	/**
	 * Defines an interface for easy access to request variable. Variable is looked up within
	 * all request parts.
	 *
	 * @return boolean
	 */
	function offsetExists($offset)
	{
		return isset($this->allVars[$offset]);
	}

	/**
	 * Defines an interface for easy access to request variable. Variable is looked up within
	 * all request parts.
	 *
	 * @return mixed
	 */
	function offsetGet($offset)
	{
		return $this->allVars[$offset];
	}

	/**
	 * Not implemented, and won't be.
	 *
	 * @return void
	 */
	function offsetSet($offset, $value)
	{
		Assert::isUnreachable('request arguments are read-only');
	}

	/**
	 * Not implemented, and won't be.
	 *
	 * @return void
	 */
	function offsetUnset($offset)
	{
		Assert::isUnreachable('request arguments are read-only');
	}

	/**
	 * Gets the unique hash for the current client who invoked the request.
	 *
	 * @param boolean whether to fasten the resulting hash to IP-address of the client of not. Default is true.
	 *
	 * @todo add ability to define custom values that affect the uniqueness of the resulting hash
	 *
	 * @return string
	 */
	function getClientHash($useIps = true)
	{
		$items = array();

		if ($useIps) {
			$items = $this->getAllClientIPs();
		}

		$uniqEnvFields = array (
	        'HTTP_USER_AGENT', 'HTTP_ACCEPT_LANGUAGE', 'HTTP_ACCEPT_CHARSET',
	        'HTTP_ACCEPT_ENCODING', 'HTTP_TE', 'HTTP_UA_CPU', 'HTTP_UA_OS', 'HTTP_UA_COLOR',
	        'HTTP_UA_PIXELS', 'HTTP_UA_VOICE',
		);

		foreach ($uniqEnvFields as $uq_field) {
			if (isset($this->serverVars[$uq_field])) {
				// use strtolower because of an IE issue: it sends
				// HTTP_ACCEPT_LANGUAGE=en_US via GET and HTTP_ACCEPT_LANGUAGE=en_us via POST
				// (different case causes wrong checksum)
				$items[] = strtolower($_SERVER[$uq_field]);
			}
			else if (isset($this->envVars[$uq_field])) {
				$items[] = strtolower($_SERVER[$uq_field]);
			}
		}

		// fasten to the current server to avoid key haching over different apps built on the framework
		$items[] = PHOEBIUS_APP_ID;

		$string = join('', $items);

		return sha1($string);
	}

	/**
	 * Tries to resolve the actual client IP.
	 *
	 * @return IP
	 */
	function getActualClientIP()
	{
		foreach (self::$row as $field) {
			if (isset($this->envVars[$field])) {
				try {
					return new IP($this->envVars[$field]);
				}
				catch (ArgumentException $e) {
					//nothing, just skip due the passed IP is not an IP
				}
			}
		}

		return $this->getRemoteAddress();
	}

	/**
	 * Gets the set of all possible client's IP-addresses used for dispatching the request.
	 *
	 * @return array of IP
	 */
	function getAllClientIPs()
	{
		$ips = array();

		$ips[] = $this->getRemoteAddress();
		foreach (self::$row as $field) {
			if (isset($this->envVars[$field])) {
				try {
					$ips[] = new IP($this->envVars[$field]);
				}
				catch (ArgumentException $e) {
					//nothing
				}
			}
		}

		return $ips;
	}

	/**
	 * Gets the list of request headers.
	 *
	 * @return array
	 */
	function getHeaders()
	{
		return getallheaders();
	}

	/**
	 * Gets the request header identified by header name.
	 *
	 * @param string $header header name
	 * @throws ArgumentException thrown when no header found by the specified name
	 * @return string
	 */
	function getHeader($header)
	{
		Assert::isScalar($header);

		$headers = $this->getHeaders();
		if (isset($headers[$header])) {
			return $headers[$header];
		}

		throw new ArgumentException('header', 'header not recognized');
	}
	
	/**
	 * Aka SERVER_ADDR
	 *
	 * @return IP
	 */
	function getServerAddress()
	{
		return $this->serverVars['SERVER_ADDR'];
	}

	/**
	 * Aka SERVER_PORT
	 *
	 * @return integer
	 */
	function getServerPort()
	{
		return $this->serverVars['SERVER_PORT'];
	}

	/**
	 * Gets the remote address that made the request.
	 *
	 * @return IP
	 */
	function getRemoteAddress()
	{
		return new IP($this->serverVars['REMOTE_ADDR']);
	}

	/**
	 * Gets the remote address that made the request.
	 *
	 * @return IP
	 */
	function getRemotePort()
	{
		return $this->serverVars['REMOTE_PORT'];
	}
}

?>