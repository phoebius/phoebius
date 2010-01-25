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
 * Represents an output stream that collects written data in internal buffer
 *
 * @ingroup Utils_Stream
 */
final class MemoryStream implements IOutput
{
	/**
	 * @var string
	 */
	private $buffer;

	function write($buffer)
	{
		$this->buffer .= $buffer;

		return $this;
	}

	/**
	 * Gets the contents of the buffer
	 *
	 * @return string
	 */
	function getBuffer()
	{
		return $this->buffer;
	}

	/**
	 * Erases the buffer
	 *
	 * @return MemoryStream
	 */
	function clean()
	{
		$this->buffer = null;

		return $this;
	}

	/**
	 * Gets the contents of the buffer
	 *
	 * @return string
	 */
	function __toString()
	{
		return $this->buffer;
	}
}

?>