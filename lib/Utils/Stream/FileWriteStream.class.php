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
 * Represents a stream that writes the contents to file
 *
 * @ingroup Utils_Stream
 */
class FileWriteStream implements IOutput
{
	/**
	 * @var string
	 */
	private $filename;

	/**
	 * @param string $filename path to a file where to append the contents
	 */
	function __construct($filename)
	{
		Assert::isScalar($filename);

		$this->filename = $filename;

		file_put_contents($filename, null);
	}

	function write($buffer)
	{
		file_put_contents($this->filename, $buffer, FILE_APPEND);

		return $this;
	}
}

?>