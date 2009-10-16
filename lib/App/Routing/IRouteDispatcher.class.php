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
interface IRouteDispatcher
{
	/**
	 * @throws RouteHandleException
	 * @return void
	 */
	function handle(IRouteContext $routeContext, IAppContext $appContext);
}

?>