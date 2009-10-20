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
 * @ingroup Core_FS
 */
class TempFile implements IWriteStream
{
	/**
	 * @var boolean
	 */
	private $keepAfterShutdown;

	/**
	 * @var string
	 */
	private $path;

	/**
	 * @return TempFile
	 */
	static function create($keepAfterShutdown = false)
	{
		return new self ($keepAfterShutdown);
	}

	function __construct($keepAfterShutdown = false)
	{
		$this->path = FSUtils::getTempFilename(__CLASS__);
		$this->keepAfterShutdown = $keepAfterShutdown;
	}

	/**
	 * @return string
	 */
	function getPath()
	{
		return $this->path;
	}

	/**
	 * @return IWriteStream
	 */
	function write($buffer)
	{
		file_put_contents($this->path, $buffer, FILE_APPEND);

		return $this;
	}

	/**
	 * @return TempFile
	 */
	function erase()
	{
		file_put_contents($this->path, null);

		return $this;
	}

	/**
	 * Path to the file
	 * @return string
	 */
	function __toString()
	{
		return $this->getPath();
	}

	function __destruct()
	{
		if (!$this->keepAfterShutdown) {
			try {
				@unlink($this->path);
			}
			catch (Exception $e) {}
		}
	}
}

?>