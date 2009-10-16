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
 * @ingroup Server
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
	 * @param WebServerStateDictionary $dictionary
	 * @param array $envVars optinal set of environment variables
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
	 * TODO: add custom fields for hashing
	 * @return string
	 */
	function getClientHash($useIps = true, $length = 40)
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

		if ($length > 40) {
			$length = 40;
		}

		return substr(sha1($string), 0, $length);
	}

	/**
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
	 * @return array of {@link IP}
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

	/**
	 * @see IServerState::getInvokedScript()
	 *
	 * @return string
	 */
	function getInvokedScriptFilename()
	{
		return $this->serverVars[WebServerStateDictionary::SCRIPT_FILENAME];
	}

	/**
	 * @see IWebServerState::getDocumentRoot()
	 *
	 * @return string
	 */
	function getDocumentRoot()
	{
		return $this->serverVars[WebServerStateDictionary::DOCUMENT_ROOT];
	}

	/**
	 * @see IWebServerState::getHeader()
	 *
	 * @param string $header
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
	 * @see IWebServerState::getHeaders()
	 *
	 * @return array
	 */
	function getHeaders()
	{
		// FIXME: move this call to WebServerStateDictionary as a default
		return getallheaders();
	}

	/**
	 * @see IWebServerState::getRemoteAddress()
	 *
	 * @return IP
	 */
	function getRemoteAddress()
	{
		return new IP($this->serverVars[WebServerStateDictionary::REMOTE_ADDR]);
	}

	/**
	 * @see IWebServerState::getRemotePort()
	 *
	 * @return integer
	 */
	function getRemotePort()
	{
		return $this->serverVars[WebServerStateDictionary::REMOTE_PORT];
	}

	/**
	 * @see IWebServerState::getServerAddr()
	 *
	 * @return IP
	 */
	function getServerAddress()
	{
		return new IP($this->serverVars[WebServerStateDictionary::SERVER_ADDR]);
	}

	/**
	 * @see IWebServerState::getServerPort()
	 *
	 * @return integer
	 */
	function getServerPort()
	{
		return $this->serverVars[WebServerStateDictionary::SERVER_PORT];
	}
}

?>