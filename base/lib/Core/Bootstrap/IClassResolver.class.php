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
interface IClassResolver
{
	/**
	 * @return string|null
	 */
	function getClassPath($classname, $hitCacheOnly = false);

	/**
	 * @return boolean
	 */
	function loadClassFile($classname, $hitCacheOnly = false);
}

?>