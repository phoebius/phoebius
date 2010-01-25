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
 * Interface wrapper over web-server state
 *
 * @ingroup App_Server
 */
interface IWebServerState extends IServerState
{
	/**
	 * Aka SCRIPT_FILENAME.
	 *
	 * @return string
	 */
	function getInvokedScriptFilename();

	/**
	 * Aka DOCUMENT_ROOT
	 *
	 * @return string
	 */
	function getDocumentRoot();

	/**
	 * Aka SERVER_ADDR
	 *
	 * @return IP
	 */
	function getServerAddress();

	/**
	 * Aka SERVER_PORT
	 *
	 * @return integer
	 */
	function getServerPort();

	/**
	 * Gets the list of request headers.
	 *
	 * @return array
	 */
	function getHeaders();

	/**
	 * Gets the request header identified by header name.
	 *
	 * @param string $header header name
	 * @throws ArgumentException thrown when no header found by the specified name
	 * @return string
	 */
	function getHeader($header);

	/**
	 * Gets the remote address that made the request.
	 *
	 * @return IP
	 */
	function getRemoteAddress();
}

?>