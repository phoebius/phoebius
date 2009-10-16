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
 * Specifies that the implementation can work like an automated class autoloader
 * @ingroup Bootstrap
 */
interface IAutoloader
{
	/**
	 * Registers the object as an autoloader. Consider using SPL ({@link spl_autoload_register})
	 * @return IAutoloader
	 */
	function register();

	/**
	 * Unregisters the object autoload
	 * @return IAutoloader
	 */
	function unregister();
}

?>