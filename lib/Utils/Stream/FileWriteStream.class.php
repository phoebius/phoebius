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
 * @ingroup Utils_Stream
 */
class FileWriteStream implements IWriteStream
{
	/**
	 * @var string
	 */
	private $filename;

	function __construct($filename)
	{
		Assert::isScalar($filename);

		$this->filename = $filename;

		file_put_contents($filename, null);
	}

	/**
	 * @return FileWriteStream
	 */
	function write($buffer)
	{
		file_put_contents($this->filename, $buffer, FILE_APPEND);

		return $this;
	}
}

?>