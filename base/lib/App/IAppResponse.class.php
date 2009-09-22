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