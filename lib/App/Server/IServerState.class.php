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
 * @ingroup Server
 */
interface IServerState
{
	/**
	 * @return ArrayObject
	 */
	function getEnvVars();

	/**
	 * Aka REQUESTIME_FORMAT
	 * @return integer
	 */
	function getRequestTime();

	/**
	 * @return array
	 */
	function getArgv();

	/**
	 * @return integer
	 */
	function getArgc();
}

?>