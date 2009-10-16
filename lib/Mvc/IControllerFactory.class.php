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
 * @ingroup Mvc
 */
interface IControllerFactory
{
	/**
	 * @return IController
	 */
	function getControllerInstance($controllerName, IControllerContext $context);
}

?>