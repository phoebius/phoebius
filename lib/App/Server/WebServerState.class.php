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
 * Encapsulates the state of a web-server.
 *
 * @ingroup App_Server
 */
class WebServerState extends CliServerState implements IWebServerState
{
	private static $fields = array
	(
		'HTTP_X_FORWARDED_FOR', 'CLIENT_IP', 'HTTP_FROM', 'HTTP_CLIENT_IP', 'HTTP_CLIENTIP',
		'HTTP_CLIENT', 'HTTP_X_FORWARDED', 'HTTP_X_DELEGATE_REMOTE_HOST', 'HTTP_SP_HOST',
	);

	/**
	 * @var array
	 */
	private $serverVars = array();

	/**
	 * @param WebServerStateDictionary dictionary of state variables
	 * @param array optinal set of environment variables
	 */
	function __construct(
			WebServerStateDictionary $dictionary,
			array $envVars = array()
		)
	{
		parent::__construct($dictionary, $envVars);

		$this->serverVars = $dictionary->getFields();
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
			// FIXME: read vars from WebServerStateDictionary instead of direct access to $_SERVER
			if (isset($_SERVER[$uq_field])) {
				$items[] = $_SERVER[$uq_field];
			}
		}

		// fasten to the current server to avoid key haching over different apps built on the framework
		$items[] = APP_GUID;

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
		$envVars = $this->getEnvVars();

		foreach (self::$fields as $field) {
			if (isset($envVars[$field])) {
				try {
					return new IP($envVars[$field]);
				}
				catch (ArgumentException $e) {
					//nothin', justa skip due the passed IP is not an IP
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
		$envVars = $this->getEnvVars();
		foreach (self::$fields as $field) {
			if (isset($envVars[$field])) {
				try {
					$ips[] = new IP($envVars[$field]);
				}
				catch (ArgumentException $e) {
					//nothin' (c) R. Plant
				}
			}
		}

		return $ips;
	}

	function getInvokedScriptFilename()
	{
		return $this->serverVars[WebServerStateDictionary::SCRIPT_FILENAME];
	}

	function getDocumentRoot()
	{
		return $this->serverVars[WebServerStateDictionary::DOCUMENT_ROOT];
	}

	function getHeader($header)
	{
		Assert::isScalar($header);

		$headers = $this->getHeaders();
		if (isset($headers[$header])) {
			return $headers[$header];
		}

		throw new ArgumentException('header', 'header not recognized');
	}

	function getHeaders()
	{
		return getallheaders();
	}

	function getRemoteAddress()
	{
		return new IP($this->serverVars[WebServerStateDictionary::REMOTE_ADDR]);
	}

	function getRemotePort()
	{
		return $this->serverVars[WebServerStateDictionary::REMOTE_PORT];
	}

	function getServerAddress()
	{
		return new IP($this->serverVars[WebServerStateDictionary::SERVER_ADDR]);
	}

	function getServerPort()
	{
		return $this->serverVars[WebServerStateDictionary::SERVER_PORT];
	}
}

?>