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
interface IWebServerState extends IServerState
{
	/**
	 * Aka SCRIPT_FILENAME
	 * @return string
	 */
	function getInvokedScriptFilename();

	/**
	 * @return string
	 */
	function getDocumentRoot();

	/**
	 * @return IP
	 */
	function getServerAddress();

	/**
	 * Aka SERVER_PORT
	 * @return integer
	 */
	function getServerPort();

	/**
	 * @return array
	 */
	function getHeaders();

	/**
	 * @return string
	 */
	function getHeader($header);
}

?>