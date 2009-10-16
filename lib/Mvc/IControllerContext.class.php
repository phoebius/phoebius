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
 * @ingroup Mvc
 */
interface IControllerContext
{
	/**
	 * @return IRouteContext
	 */
	function getRouteContext();

	/**
	 * @return IAppContext
	 */
	function getAppContext();
}

?>