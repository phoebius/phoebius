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
 * @ingroup Bootstrap
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