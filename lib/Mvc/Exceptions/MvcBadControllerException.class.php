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
 * @ingroup MvcExceptions
 */
class MvcBadControllerException extends MvcControllerDispatchException
{
	function __construct(IControllerContext $context, $controllerName, $controllerClassName)
	{
		parent::__construct($context, $controllerName, $controllerClassName, 'controller does not follow the conventions');
	}
}

?>