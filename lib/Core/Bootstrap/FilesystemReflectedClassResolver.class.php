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
 * Implements class resolver for classes which names partially reflect the path to the file where
 * those classes reside
 *
 * @ingroup Core_Bootstrap
 */
abstract class FilesystemReflectedClassResolver extends ClassResolver
{
	/**
	 * Makes the possible path to the file containing the requested class
	 * @param string name of the class
	 * @return string possible path to the file containing the requested class
	 */
	abstract function canonizeClassName($classname);

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