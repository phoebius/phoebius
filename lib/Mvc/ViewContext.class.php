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