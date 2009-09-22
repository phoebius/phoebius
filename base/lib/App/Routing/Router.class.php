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