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
 * Encapsulates the HTTP response status.
 *
 * @todo expand the list of supported status codes
 *
 * @ingroup App_Web
 */
final class HttpStatus extends Enumeration
{
	/**
	 * "Not Found" status
	 */
	const CODE_404 = 404;

	/**
	 * "Internal Server Error" status
	 */
	const CODE_500 = 500;

	private static $statusMessages = array(
		self::CODE_404 => 'Not Found',
		self::CODE_500 => 'Internal Server Error',
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