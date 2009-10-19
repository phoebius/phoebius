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
class Router
{
	/**
	 * @var IRoutingPolicy
	 */
	private $routingPolicy;

	/**
	 * @return Router
	 */
	static function create()
	{
		return new self;
	}

	/**
	 * @return Router
	 */
	function setRoutingPolicy(IRoutingPolicy $routingPolicy)
	{
		$this->routingPolicy = $routingPolicy;

		return $this;
	}

	/**
	 * @return IRoutingPolicy
	 */
	function getRoutingPolicy()
	{
		Assert::isFalse(empty($this->routingPolicy), 'routing policy not yet set');

		return $this->routingPolicy;
	}

	/**
	 * @throws RequestRewriteException
	 * @throws RoutingException
	 * @return RouteContext
	 */
	function route(IAppContext $appContext)
	{
		$request = $appContext->getRequest();
		$ruleContext = $this->getRoutingPolicy()->getMatchedRuleContext($request);
		return new RouteContext(
			$ruleContext->getRule()->rewrite($request, new Route()),
			$ruleContext
		);
	}
}

?>