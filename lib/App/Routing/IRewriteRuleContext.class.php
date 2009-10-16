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
interface IRewriteRuleContext
{
	/**
	 * @return string
	 */
	function getRuleName();

	/**
	 * @return IRequestRewriteRule
	 */
	function getRule();

	/**
	 * @return IRoutingPolicy
	 */
	function getRoutingPolicy();

	/**
	 * @return IAppRequest
	 */
	function getRequest();
}

?>