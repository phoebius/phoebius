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
 * @ingroup Core_Bootstrap
 */
abstract class FilesystemReflectedClassResolver extends ClassResolver
{
	/**
	 * Re
	 * @param string $classname
	 * @return string
	 */
	abstract function canonizeClassName($classname);

	/**
	 * Searches for the file containing the requested class withing the specified directory
	 * @param string $classname
	 * @param string $rootDirectory
	 * @return string|null returns the absolute path to the file containing the requested class
	 * 	or NULL if such file not found
	 */
	protected function findFilePath($classname, $rootDirectory)
	{
		$subPath = $this->canonizeClassName($classname) . '.' . $this->getExtension();
		$path = $rootDirectory . DIRECTORY_SEPARATOR . $subPath;

		return is_file($path)
			? $path
			: null;
	}
}

?>