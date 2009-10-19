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
 * @ingroup App_Server
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