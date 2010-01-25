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
 * Abstract string macros
 *
 * @ingroup Utils
 */
final class StringUtils extends StaticClass
{
	/**
	 * Unix line delimiter
	 */
	const DELIM_UNIX = "\n";

	/**
	 * Windows line delimiter
	 */
	const DELIM_WIN = "\r\n";

	/**
	 * Locale line delimiter
	 */
	const DELIM_LOCALE = PHP_EOL;

	/**
	 * Represents an empty string
	 */
	const EMPTY_STRING = '';

	/**
	 * Line delimiter to use as standart for the application
	 */
	const DELIM_STANDART = self::DELIM_UNIX;
}

?>