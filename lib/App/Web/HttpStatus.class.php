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
 * @ingroup App_Web
 */
class HttpStatus extends Enumeration
{
	const STATUS_404 = 404;
	const STATUS_500 = 500;
	
	private static $statusMessages = array(
		self::STATUS_404 => 'Not Found',
		self::STATUS_500 => 'Internal Server Error',		
	);
	
	/**
	 * @return string
	 */
	function getStatusMessage()
	{
		return self::$statusMessages[$this->getValue()];
	}
}

?>