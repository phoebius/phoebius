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
 * Represents a redirection to a route
 * @ingroup Mvc_ActionResults
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