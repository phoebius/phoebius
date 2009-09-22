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
 * Represents a redirection to an action
 * @ingroup ActionResults
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