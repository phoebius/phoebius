<?php
/* ***********************************************************************************************
 *
 * Phoebius Framework
 *
 * **********************************************************************************************
 *
 * Copyright (c) 2010 phoebius.org
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
 * A simple file logger. Logs the given message to the specified file prepending it with a
 * new line.
 *
 * @ingroup Utils_Log
 */
class FileLogger implements ILogger
{
	private $filepath;

	/**
	 *
	 * @param string $filepath path to the file to log messages to
	 */
	function __construct($filepath)
	{
		$this->filepath = $filepath;
	}

	function log($string)
	{
		$string =
			date('[d.m.Y H:i:s]')
			. trim($string)
			. PHP_EOL;

		file_put_contents($this->filepath, $string, FILE_APPEND);

		return $this;
	}
}

?>