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
interface IAppContext
{
	/**
	 * @return IAppRequest
	 */
	function getRequest();

	/**
	 * @return IAppResponse
	 */
	function getResponse();

	/**
	 * @return IServerState
	 */
	function getServer();
}

?>