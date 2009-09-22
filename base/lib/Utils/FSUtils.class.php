<?php
/* ***********************************************************************************************
 *
 * Phoebius Framework
 *
 * **********************************************************************************************
 *
 * Copyright notice
 *
 ************************************************************************************************/

/**
 * Filesystem utils
 * @ingroup Utils
 */
final class FSUtils extends StaticClass
{
	/**
	 * Replaces unsafe characters from the name to make the name a valid filesystem identifier
	 * @param string $name
	 * @param string $replacement unsafe characters to be replced with this one
	 * @param string $defaultIfNull default name of the identifier is the sanitized name becomes
	 * 	an empty string
	 * @return string
	 */
	static function sanitizeName($name, $replacement = '_', $defaultIfNull = null)
	{
		Assert::isScalar($name);
		Assert::isScalar($replacement);
		Assert::isScalarOrNull($defaultIfNull);


		$name = str_replace(
			array('~', '`', '#', '$', '%', '^', '*', ':', '"', '|', '\\', '/', '<', '>', '?'),
			$replacement,
			$name
		);

		if (empty($name)) {
			$name = $defaultIfNull;
		}

		return $name;
	}

	/**
	 * Clears the path unwinding "." and ".." links
	 * @param string $path
	 * @param string $separator
	 * @return string
	 */
	static function clearPath($path, $separator = DIRECTORY_SEPARATOR)
	{
		Assert::isScalar($path);
		Assert::isScalar($separator);

		$path = str_replace(array("/","\\"), $separator, $path);
		$elts = explode($separator, $path);
		$elts2 = array();
		foreach($elts as $elt) {
			switch( $elt ) {
				case "":
					{
						if ( sizeof($elts2) == 0 )
						{
							//this portion of code is needed when
							//we received  a path that begins from the OS root
							//(e.g., "/usr/local")
							$elts2[] = '';
						}
						break;
					}
				case ".":
					{
						//do nothin
						break;
					}
				case "..":
					{
						array_pop($elts2);
						break;
					}
				default:
					{
						$elts2[] = $elt;
						break;
					}
			}
		}

		$path = implode($separator, $elts2);
		return $path;
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
		Assert::isScalar($dir);

		try {
			foreach ((array)scandir($dir) as $file) {
				if ( $file != "." && $file != ".." ) {
					$path = $dir . DIRECTORY_SEPARATOR . $file;
					if (is_dir($path)) {
						self::removeDirectory($path);
					}
					else {
						self::cleanDirectory($path);
					}
				}
			}
		}
		catch (ExecutionContextException $e) {
			throw new FSOperationException("Cannot read {$dir}: {$e->getMessage()}");
		}

		try {
			rmdir($dir);
		}
		catch (ExecutionContextException $e) {
			throw new FSAccessException("Cannot remove {$dir} (though it should be empty now): {$e->getMessage()}");
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

		$where = PathResolver::getInstance()->getTmpDir(__CLASS__);
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

		$directory = PathResolver::getInstance()->getTmpDir(__CLASS__);

		$attempts = 5;

		do {
			--$attempts;
			$path = $directory . DIRECTORY_SEPARATOR . $prefix . stopwatch() . mt_rand();
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