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
 * Encapsulates the HTTP response status.
 *
 * @todo expand the list of supported status codes
 *
 * @ingroup App_Web
 */
final class HttpStatus extends Enumeration
{
	/**
	 * Moved Permanently
	 */
	const CODE_301 = 301;
	
	/**
	 * Found
	 */
	const CODE_302 = 302;
	
	/**
	 * See Other
	 */
	const CODE_303 = 303;
	
	/**
	 * Not Found
	 */
	const CODE_404 = 404;
	
	/**
	 * Internal Server Error
	 */
	const CODE_500 = 500;
	
	/**
	 * Service Unavailable
	 */
	const CODE_503 = 503;

	private static $statusMessages = array(
		self::CODE_301 => 'Moved Permanently',
		self::CODE_302 => 'Found',
		self::CODE_303 => 'See Other',
		self::CODE_404 => 'Not Found',
		self::CODE_500 => 'Internal Server Error',
		self::CODE_503 => 'Service Unavailable'
	);

	/**
	 * Gets the code of the status
	 *
	 * @return int
	 */
	function getStatusCode()
	{
		return $this->getValue();
	}

	/**
	 * Gets the message of the status
	 *
	 * @return string
	 */
	function getStatusMessage()
	{
		return self::$statusMessages[$this->getValue()];
	}
}

?>