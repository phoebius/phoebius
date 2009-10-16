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
class MvcControllerNotFoundException extends MvcControllerDispatchException
{
	function __construct(IControllerContext $context, $controllerName, $controllerClassName)
	{
		parent::__construct($context, $controllerName, $controllerClassName, 'controller not found');
	}
}

?>