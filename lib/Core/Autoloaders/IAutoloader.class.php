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
 * Specifies that the implementation can work like an automated class autoloader
 * @ingroup Core_Bootstrap
 */
interface IAutoloader
{
	/**
	 * Registers the object as autoloader. Consider using SPL (spl_autoload_register())
	 * @return void
	 */
	function register();

	/**
	 * Unregisters the object autoload
	 * @return void
	 */
	function unregister();
}

?>