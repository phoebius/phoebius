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
 * Represents a temporary file
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
		$where = PHOEBIUS_TMP_ROOT;
		$filepath = tempnam($where, microtime(true));

		if (!$filepath) {
			throw new StateException("Failed to create tempnam in {$where}");
		}
		
		$this->path = $filepath;
		$this->keepAfterShutdown = $keepAfterShutdown;

		if (!$keepAfterShutdown) {
			register_shutdown_function(
				array($this, 'unlink')
			);
		}
	}

	/**
	 * Gets the path to temporary file
	 *
	 * @return string
	 */
	function getPath()
	{
		return $this->path;
	}

	function write($buffer)
	{
		file_put_contents($this->path, $buffer, FILE_APPEND);

		return $this;
	}

	/**
	 * Erases the contents of the temporary file
	 *
	 * @return TempFile
	 */
	function erase()
	{
		file_put_contents($this->path, null);

		return $this;
	}

	/**
	 * Unlinks the temporary file
	 *
	 * @return void
	 */
	function unlink()
	{
		try {
			@unlink($this->path);
		}
		catch (Exception $e) {}
	}

	/**
	 * Path to the file
	 *
	 * @return string
	 */
	function __toString()
	{
		return $this->getPath();
	}
}

?>