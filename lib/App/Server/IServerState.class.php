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
 * Interface wrapper over internal server state
 *
 * @ingroup App_Server
 */
interface IServerState
{
	/**
	 * @return array
	 */
	function getEnvVars();

	/**
	 * Aka REQUEST_TIME
	 *
	 * @return integer
	 */
	function getRequestTime();

	/**
	 * Aka $argv.
	 *
	 * Gets the list of arguments passed to the script.
	 *
	 * @return array
	 */
	function getArgv();

	/**
	 * Aka $argc.
	 *
	 * Gets the number of arguments passed to the script.
	 *
	 * @return integer
	 */
	function getArgc();
}

?>