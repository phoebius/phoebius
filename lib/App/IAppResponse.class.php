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
 * @ingroup App
 */
interface IAppResponse
{
	/**
	 * @return boolean
	 */
	function isFinished();

	/**
	 * @return IAppResponse an object itself
	 */
	function flush();

	/**
	 * @return IAppResponse an object itself
	 */
	function clean();

	/**
	 * @return void
	 */
	function finish();

	/**
	 * @return IAppResponse an object itself
	 */
	function out($string);

	/**
	 * @return IAppResponse an object itself
	 */
	function outFile($filepath);

	/**
	 * @return IAppResponse an object itself
	 */
	function openBuffer();

	/**
	 * @return IAppResponse an object itself
	 */
	function flushBuffer();

	/**
	 * @return IAppResponse an object itself
	 */
	function dropBuffer();

	/**
	 * @return boolean
	 */
	function isBufferOpened();
}

?>