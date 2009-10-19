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
 * @ingroup App_Routing
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