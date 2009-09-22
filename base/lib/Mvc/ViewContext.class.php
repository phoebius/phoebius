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
class ViewContext extends ControllerContext implements IViewContext
{
	/**
	 * @var IController
	 */
	private $controller;

	/**
	 * @var Model
	 */
	private $model;

	function __construct(
			IController $controller,
			Model $model,
			IRouteContext $routeContext,
			IAppContext $appContext
		)
	{
		parent::__construct($routeContext, $appContext);

		$this->controller = $controller;
		$this->model = $model;
	}

	/**
	 * @return IController
	 */
	function getController()
	{
		return $this->controller;
	}

	/**
	 * @return Model
	 */
	function getModel()
	{
		return $this->model;
	}
}

?>