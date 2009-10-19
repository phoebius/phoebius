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
 * Represents a redirection to an action
 * @ingroup Mvc_ActionResults
 */
class RedirectToActionResult extends RedirectToRouteResult
{
	function __construct(ActionBasedController $controller, $action, IControllerContext $controllerContext, array $parameters = array())
	{
		$parameters[MvcDispatcher::PARAMETER_CONTROLLER_NAME] = get_class($controller);
		$parameters[ActionBasedController::PARAMETER_ACTION] = $action;

		parent::__construct(
			$controllerContext->getRouteContext()->getRewriteRuleContext()->getRuleName(),
			$controllerContext,
			$parameters
		);
	}
}

?>