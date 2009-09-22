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
 * Represents a redirection to a route
 * @ingroup ActionResults
 */
class RedirectToRouteResult extends RedirectResult
{
	function __construct($ruleName, IControllerContext $controllerContext, array $parameters = array())
	{
		Assert::isScalar($ruleName);

		$rrc = $controllerContext->getRouteContext()->getRewriteRuleContext();
		$rule = $rrc->getRoutingPolicy()->getRule($ruleName);
		$route = new Route;

		$request = $rule->compose(
			$route->setParameters($parameters),
			$rrc->getRequest()->getCleanCopy()
		);

		parent::__construct($request->getHttpUrl());
	}
}

?>