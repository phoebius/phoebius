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
interface IViewContext extends IControllerContext
{
	/**
	 * @return IController
	 */
	function getController();

	/**
	 * @return Model
	 */
	function getModel();
}

?>