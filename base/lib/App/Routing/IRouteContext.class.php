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
 * @ingroup Routing
 */
interface IRouteContext
{
	/**
	 * @return Route
	 */
	function getRoute();

	/**
	 * @return IRewriteRuleContext
	 */
	function getRewriteRuleContext();

	/**
	 * @return IAppRequest
	 */
	function composeRequest();
}

?>