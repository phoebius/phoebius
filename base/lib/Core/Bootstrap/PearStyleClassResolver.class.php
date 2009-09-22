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
class PearStyleClassResolver extends FilesystemReflectedClassResolver
{
	/**
	 * @param string $classname
	 * @return string
	 */
	function canonizeClassName($classname)
	{
		return str_replace('_', '/', $classname);
	}
}

?>