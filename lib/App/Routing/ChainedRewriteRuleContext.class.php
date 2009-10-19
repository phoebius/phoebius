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
class ChainedRewriteRuleContext extends RewriteRuleContext
{
	/**
	 * @var IRouteDispatcher
	 */
	private $dispatcher;

	function __construct(
			IRouteDispatcher $dispatcher,
			IRoutingPolicy $policy,
			$name,
			IAppRequest $request
		)
	{
		parent::__construct($policy, $name, $request);

		$this->dispatcher = $dispatcher;
	}

	/**
	 * @return IRouteDispatcher
	 */
	function getRouteDispatcher()
	{
		return $this->dispatcher;
	}

	/**
	 * @return void
	 */
	function handle(Route $route, IAppContext $appContext)
	{
		$this->getRouteDispatcher()->handle(new RouteContext($route, $this), $appContext);
	}
}

?>