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
 * Response interface for application that run at web-server.
 *
 * @ingroup App_Web
 */
interface IWebResponse extends IAppResponse
{
	/**
	 * Checks whether response headers are already sent.
	 *
	 * @return boolean
	 */
	function isHeadersSent();

	/**
	 * Gets the list of headers that are ready to be sent within the response
	 *
	 * @return array
	 */
	function getHeaders();

	/**
	 * Appends the header to be sent to client within the response
	 *
	 * @param string $header name of the header
	 * @param string $value value of the header
	 * @return IWebResponse itself
	 */
	function addHeader($header, $value);

	/**
	 * Adds the list of headers to be sent to client within the response
	 *
	 * @param array $headers associative key=>value array of headers, where key is the name of the header
	 *
	 * @return IWebResponse
	 */
	function addHeaders(array $headers);

	/**
	 * Sets the status of the respose
	 *
	 * @param HttpStatus $status status to be set
	 *
	 * @return IWebResponse
	 */
	function setStatus(HttpStatus $status);

	/**
	 * Sends the redirect.
	 *
	 * Note that this function does not close the connection and terminates the execution.
	 *
	 * @param HttpUrl $url the redirection dest.
	 *
	 * @return void
	 */
	function redirect(HttpUrl $url);
}

?>