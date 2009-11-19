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
 * Represents a temp file.
 *
 * @todo file is unlinked on object destruction (in case when $keepAfterShutdown = false). This is
 * 		not good behaviour - we should drop the file using register_shutdown_function
 *
 * @ingroup Core_FS
 */
class TempFile implements IOutput
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
	 * @param boolean whether to keep the file after application termination or not
	 */
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
	 * @return IOutput
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