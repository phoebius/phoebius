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
interface IRequestRewriteRule
{
	/**
	 * @return boolean
	 */
	function isMatch(IAppRequest $request);

	/**
	 * Rewrites the specified request to the resulting route specified
	 * @throws RequestRewriteException
	 * @return Route
	 */
	function rewrite(IAppRequest $request, Route $route);

	/**
	 * Casts the specified route to the request
	 * @throws RouteHandleException
	 * @return IAppRequest
	 */
	function compose(Route $route, IAppRequest $request);
}

?>