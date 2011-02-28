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
 * Implements a class resolving mechanism used to search files containing the requested classes
 * @ingroup Core_Bootstrap
 */
abstract class ClassResolver implements IClassResolver
{
	private $paths = array();
	private $extension = 'php';
	private $useIncludePath = true;

	/**
	 * Searches for the file containing the requested class withing the specified directory
	 * @param string class to be found
	 * @param string directory to be looked up
	 * @return string|null absolute path to the file containing the requested class
	 * 	or NULL if such file not found
	 */
	abstract protected function findFilePath($classname, $rootDirectory);

	/**
	 * @param boolean whether to use include path. Default it true.
	 */
	function __construct($useIncludePath = true)
	{
		Assert::isBoolean($useIncludePath);

		$this->useIncludePath = $useIncludePath;
	}

	function getClassPath($classname)
	{
		Assert::isScalar($classname);

		$classpath = $this->scanClassPaths($classname);

		return $classpath;
	}

	/**
	 * Adds the custom path to be scanned while resolving the files containing classes
	 * @param string $path
	 * @return ClassResolver
	 */
	function addPath($path)
	{
		foreach (explode(PATH_SEPARATOR, $path) as $path) {
			$this->paths[] = $path;
		}

		return $this;
	}

	/**
	 * Gets the extension that is postfixed to the file names containing classes while resolving
	 * @return string
	 */
	function getExtension()
	{
		return $this->extension;
	}

	/**
	 * Sets the extension (excluding the dot) to be postfixed to the file names containing classes
	 * while resolving
	 * @param string $extension
	 * @return ClassResolver
	 */
	function setExtension($extension)
	{
		Assert::isScalar($extension);

		$this->extension = ltrim($extension, '.');

		return $this;
	}

	/**
	 * Returns the list of custom paths to be scanned while resolving the classes
	 * @return array
	 */
	private function getActualPaths()
	{
		return array_merge(
			$this->useIncludePath
				? explode(PATH_SEPARATOR, get_include_path())
				: array(),
			$this->paths
		);
	}

	/**
	 * Scans the paths for the given class name
	 * @return string|null
	 */
	private function scanClassPaths($classname)
	{
		foreach($this->getActualPaths() as $path) {
			if (($filePath = $this->findFilePath($classname, $path))) {
				return $filePath;
			}
		}

		return null;
	}
}
