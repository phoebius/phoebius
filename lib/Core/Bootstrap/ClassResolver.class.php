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
 * Implements a class resolving mechanism used to search files containing the requested classes
 * @ingroup Core_Bootstrap
 */
abstract class ClassResolver extends InternalSegmentCache implements IClassResolver
{
	private $foundClasspaths = array();
	private $paths = array();
	private $extension = 'php';
	private $useIncludePath = true;
	private $exclusionRegexps = array(
		'/^\./'
	);

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

	/**
	 * Drops a resolver cache
	 * @return ClassResolver an object itself
	 */
	function clearCache()
	{
		$this->dropCache();

		return $this;
	}

	function getClassPath($classname, $useCacheOnly = false)
	{
		return $this->resolveClassPath($classname, true, $useCacheOnly);
	}

	function loadClassFile($classname, $useCacheOnly = false)
	{
		$classpath = $this->resolveClassPath($classname, false, $useCacheOnly);

		if ($classpath) {
			try {
				include $classpath;

				return true;
			}
			catch (ExecutionContextException $e) {
				if ($e->getSeverity() == E_WARNING) {
					unset($this->foundClasspaths[$classname]);
					$this->uncache($classname);

					$classpath = $this->resolveClassPath($classname, true, false);

					if ($classpath) {
						include $classpath;

						return true;
					}
				}

				throw $e;
			}
		}

		return false;
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
	 * Adds a regexp that is applied for each directory entry to skip it
	 * @param string $regexp
	 * @return ClassResolver
	 */
	function addExludeRegexp($regexp)
	{
		Assert::isScalar($regexp);

		$this->exclusionRegexps[] = $regexp;

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
	 * Gets the identifier of the class resolver, based on the paths to be scanned while resolving
	 * @return string
	 */
	function getId()
	{
		//sort the path list to avoid different Ids between equal resolvers
		$paths = $this->paths;
		sort($paths);

		return sha1(
			   APP_GUID
			 . get_class($this)
			 . $this->extension
			 . join(PATH_SEPARATOR, $paths)
			 . ($this->useIncludePath ? get_include_path() : 0)
		);
	}

	/**
	 * Gets the unique identifier of the class that needed the cache
	 * @return scalar
	 */
	protected function getCacheId()
	{
		return $this->getId();
	}

	/**
	 * Returns the list of custom paths to be scanned while resolving the classes
	 * @return array
	 */
	function getActualPaths()
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
			if ($this->isSkippable($path)) {
				continue;
			}

			if (($filePath = $this->findFilePath($classname, $path))) {
				return $filePath;
			}
		}

		return null;
	}

	/**
	 * @return boolean
	 */
	protected function isSkippable($path)
	{
		if (!is_dir($path)) {
			return true;
		}

		$base = basename($path);
		foreach ($this->exclusionRegexps as $exclusionMask) {
			try {
				return preg_match($exclusionMask, $base);
			}
			catch (ExecutionContextException $e) {
				Assert::isUnreachable(
					'wrong regexp given as an exclusion mask: %s',
					$exclusionMask
				);
			}
		}
	}

	/**
	 * Resolves the file path containing the requested class. Firstly, searches withing the cache
	 * provided by {@link InternalSegmentCache}, then invokes the path scanner and comparer
	 * provided by a descendant class
	 * @param string $classname
	 * @param boolean $checkIfExists
	 * @return string|null returns the absolute path to the file containing the requested class
	 * 	or NULL if such file not found
	 */
	private function resolveClassPath($classname, $checkIfExists, $useCacheOnly = false)
	{
		Assert::isScalar($classname);
		Assert::isBoolean($checkIfExists);

		if (isset($this->foundClasspaths[$classname])) {
			return $this->foundClasspaths[$classname];
		}

		$classpath = null;
		if ($this->isCached($classname)) {
			$classpath = $this->getCached($classname);
			if ($checkIfExists && $classpath) {
				if (!file_exists($classpath)) {
					$this->uncache($classname);
					$classpath = null;
				}
			}
		}

		if (!$classpath && !$useCacheOnly) {
			$classpath = $this->scanClassPaths($classname);

			if ($classpath) {
				$this->cache($classname, $classpath);
			}
		}

		if ($classpath) {
			$this->foundClasspaths[$classname] = $classpath;
		}

		return $classpath;
	}
}

?>