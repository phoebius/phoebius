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
class RouteContext implements IRouteContext
{
	/**
	 * @var Route
	 */
	private $route;

	/**
	 * @var IRewriteRuleContext
	 */
	private $rewriteRuleContext;

	function __construct(Route $route, IRewriteRuleContext $context)
	{
		$this->route = $route;
		$this->rewriteRuleContext = $context;
	}

	/**
	 * @return Route
	 */
	function getRoute()
	{
		return $this->route;
	}

	/**
	 * @return IRewriteRuleContext
	 */
	function getRewriteRuleContext()
	{
		return $this->rewriteRuleContext;
	}

	/**
	 * @return IAppRequest
	 */
	function composeRequest()
	{
		return $this
			->getRewriteRuleContext()
			->getRule()
			->compose(
				$this,
				$this->getRewriteRuleContext()->getRequest()->getCleanCopy()
			);
	}
}

?>