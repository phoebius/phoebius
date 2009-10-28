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
final class MemoryStream implements IOutput
{
	/**
	 * @var string
	 */
	private $buffer;

	/**
	 * @return FileWriteStream
	 */
	function write($buffer)
	{
		$this->buffer .= $buffer;

		return $this;
	}

	/**
	 * @return string
	 */
	function getBuffer()
	{
		return $this->buffer;
	}

	/**
	 * @return MemoryStream
	 */
	function clean()
	{
		$this->buffer = null;

		return $this;
	}

	/**
	 * @return string
	 */
	function __toString()
	{
		return $this->buffer;
	}
}

?>