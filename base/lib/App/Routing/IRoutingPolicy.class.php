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
interface IRoutingPolicy
{
	/**
	 * @throws ArgumentException
	 * @return IRequestRewriteRule
	 */
	function getRule($name);

	/**
	 * @return array of {@link IRequestRewriteRule}
	 */
	function getRules();

	/**
	 * @throws RoutingException
	 * @return IRewriteRuleContext
	 */
	function getMatchedRuleContext(IAppRequest $request);
}

?>