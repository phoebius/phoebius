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
 * Filesystem utilities
 * @aux
 * @ingroup Utils
 */
final class FSUtils extends StaticClass
{
	/**
	 * Cleans the directory contents completely
	 * @throws FSOperationException
	 * @return void
	 */
	static function cleanDirectory($dir)
	{
		Assert::isScalar($dir);

		try {
			foreach ((array)scandir($dir) as $file) {
				if ( $file != "." && $file != ".." ) {
					$path = $dir . DIRECTORY_SEPARATOR . $file;
					if (is_dir($path)) {
						self::removeDirectory($path);
					}
					else {
						unlink($path);
					}
				}
			}
		}
		catch (ExecutionContextException $e) {
			//throw new FSOperationException("Cannot read {$dir}: {$e->getMessage()}");
		}
	}

	/**
	 * Removes a directory recursively
	 * @param string $dir
	 * @return string
	 * @throws FSOperationException
	 * @throws FSAccessException
	 */
	static function removeDirectory($dir)
	{
		self::cleanDirectory($dir);

		try {
			rmdir($dir);
		}
		catch (ExecutionContextException $e) {
			//throw new FSAccessException("Cannot remove {$dir} (though it should be empty now): {$e->getMessage()}");
		}
	}

	/**
	 * Creates a temporary file, and returns the path to it
	 * @throws FSOperationException
	 * @param string $prefix
	 * @return string
	 */
	static function getTempFilename($prefix = '')
	{
		Assert::isScalar($prefix);

		$where = PathResolver::getInstance()->getTmpDir($prefix);
		$filepath = tempnam($where, $prefix);

		if (!$filepath) {
			throw new FSOperationException("Failed to create temporary file in {$where}");
		}

		return $filepath;
	}

	/**
	 * Creates a temporary directory and returns a direct path to it
	 * @throws FSOperationException
	 * @param string $prefix
	 * @return string
	 */
	static function getTempDirectory($prefix = '')
	{
		Assert::isScalar($prefix);

		$directory = PathResolver::getInstance()->getTmpDir($prefix);

		$attempts = 5;
		$path = null;

		do {
			--$attempts;
			$path = $directory . DIRECTORY_SEPARATOR . $prefix . microtime(true) . mt_rand();
		} while (
			!mkdir($path, 0700, true)
			&& $attempts > 0
			&& !usleep(100)
		);

		if ($attempts == 0) {
			throw new FSOperationException("failed to create subdirectory in {$directory}");
		}

		return $path;
	}

}

?>