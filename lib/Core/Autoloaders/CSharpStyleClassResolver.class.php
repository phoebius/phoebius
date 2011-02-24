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
 * Implements the class resolving mechanism, that looks like most of C# project structures, i.e.
 * one class is stored withing a separate file with the name of the class and a simple file
 * extension. E.g. class CSharpStyleClassResolver could be found within
 * CSharpStyleClassResolver.class.php. This class is optimized with pre-caching and pre-scanning
 * @ingroup Core_Bootstrap
 */
final class CSharpStyleClassResolver extends ClassResolver
{
	/**
	 * List of classes resolved during the directory scan. This needed to avoid multiple
	 * scans of the passed directories. Represents a hash where keys are the classnames and values
	 * are the direct path to files where the corresponding classes are defined
	 * @var array
	 */
	private $preCached = array();

	/**
	 * List of completely scanned paths
	 * @var array
	 */
	private $scannedPaths = array();

	/**
	 * @var boolean
	 */
	private $allowPreScan = false;

	/**
	 * @param boolean whether to use include path or not
	 * @param boolean whether to allow pre-scanning technique or not. PreScan is important for huge and distributed class locations
	 */
	function __construct($useIncludePath = true, $allowPreScan = false)
	{
		Assert::isBoolean($allowPreScan);

		$this->allowPreScan = $allowPreScan;

		parent::__construct($useIncludePath);

		$this->setExtension(PHOEBIUS_TYPE_EXTENSION);
	}

	/**
	 * A factory-like helper constructor
	 * @return CSharpStyleClassResolver
	 */
	static function create($useIncludePath = true, $allowPreScan = false)
	{
		return new self ($useIncludePath, $allowPreScan);
	}

	protected function findFilePath($classname, $rootDirectory)
	{
		// USE PRE CACHE
		if (isset($this->preCached[$classname])) {
			return $this->preCached[$classname];
		}
		// END USE PRE CACHE

		// check if this directory is scanned or not
		// this is a bogus check due this logic is
		// already implemented in an overloaded isSkippable() method
		if (in_array($rootDirectory, $this->scannedPaths)) {
			return null;
		}

		$dottedExtension = '.' . $this->getExtension();
		$dottedExtensionLength = strlen($dottedExtension);

		$filepath = null;
		$filename = $classname . $dottedExtension;

		$existingFilepath = null;

		$currentPreScanIdx = (sizeof($this->scannedPaths) > 0)
			? sizeof($this->scannedPaths) - 1
			: 0;

		foreach ((array)scandir($rootDirectory) as $item) {
			$currentIterationPath = $rootDirectory . '/' . $item;

			// PRE CACHE
			// invoke precache when filename is at least longer then the extension
			if (strlen($item) > $dottedExtensionLength) {
				if (substr($item, -1 * $dottedExtensionLength) == $dottedExtension) {
					$preCachedClassname = substr($item, 0, -1 * $dottedExtensionLength);
					$this->preCached[$preCachedClassname] = $currentIterationPath;
				}
			}
			// END PRE CACHE

			if (
					   $filename == $item
					&& is_file($currentIterationPath)
			) {
				$filepath = $currentIterationPath;
			}
			else {
				if ($this->isSkippable($currentIterationPath)) {
					$this->scannedPaths[] = $currentIterationPath;

					continue;
				}

				if (is_dir($currentIterationPath)) {
					$filepath = $this->findFilePath($classname, $currentIterationPath);
				}
			}

			// we get only the first found result, never overwrite found path with a nested one
			if ($filepath && !$existingFilepath) {

				$existingFilepath = $filepath;

				if (!$this->allowPreScan) {
					break;
				}
			}
		}

		// check the directory as completely scanned only in two cases:
		// 1. pre-scan is allowed (so that we surely prescanned the directory even if we have found the path before)
		// 2. we didn't found the filepath to a class (it means that we scanned the directory as deep as possible)
		if ($this->allowPreScan /* || !$existingFilepath */) {
			// drop paths to a nested dirs - we would never go thru them again
			// because the path of a curent level will prevent this
			$this->scannedPaths = array_slice($this->scannedPaths, $currentPreScanIdx);
			$this->scannedPaths[] = $rootDirectory;
		}

		return $existingFilepath;
	}

	/**
	 * @return boolean
	 */
	protected function isSkippable($path)
	{
		return
			   (in_array($path, $this->scannedPaths))
			|| (parent::isSkippable($path));
	}
}

?>