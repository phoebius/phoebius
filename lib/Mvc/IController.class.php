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
interface IController
{
	/**
	 * @throws RouteHandleException
	 * @return void
	 */
	function handle(IControllerContext $context);
}

?>