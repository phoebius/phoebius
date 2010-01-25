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
 * Resolves the system paths to be shared between application components
 * @deprecated
 * @ingroup Core_Bootstrap
 */
final class PathResolver extends LazySingleton
{
	/**
	 * Gets the instance of singleton class
	 * @return PathResolver itself
	 */
	static function getInstance()
	{
		return LazySingleton::instance(__CLASS__);
	}

	/**
	 * Returns the absolute path to a temp directory for a separate component specified by the
	 * class (or even it's object). To reflect internal temp structure, it is possible to specify
	 * the relative path to a directory inside a separate directory that is shared for the
	 * specified component, and it will be merged to an absolute path and created if needed.
	 * @param string|object $class to which the temporary directory should be shared
	 * @param string $internalDirectory a directory insided shared directory to be created
	 * @return string an absolute path to a separate temporary directory
	 */
	function getTmpDir($class, $internalDirectory = null)
	{
		return $this->getDir($class, APP_TMP_ROOT, $internalDirectory);
	}

	/**
	 * @return string
	 */
	private function getDir($class, $scope, $internalDirectory = null)
	{
		Assert::isScalarOrNull($internalDirectory);

		$absolutePath = array(
			$scope,
			$this->getActualClassName($class)
		);

		if ($internalDirectory) {
			$absolutePath[] = (string) $internalDirectory;
		}

		$absolutePath = join(DIRECTORY_SEPARATOR, $absolutePath);

		if (!is_dir($absolutePath)) {
			mkdir($absolutePath, 0755, true);
		}

		Assert::isTrue(
			is_writable($absolutePath),
			'directory %s is not writable',
			$absolutePath
		);

		return $absolutePath;
	}

	/**
	 * @return string
	 */
	private function getActualClassName($class)
	{
		if (!is_scalar($class)) {
			Assert::isTrue(is_object($class));

			$class = get_class($class);
		}

		return $class;
	}
}

?>