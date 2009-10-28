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
class ViewContext implements IViewContext
{
	/**
	 * @var IController|null
	 */
	private $controller;

	/**
	 * @var Model
	 */
	private $model;

	/**
	 * @var IOutput
	 */
	private $ws;

	function __construct(
			Model $model,
			IOutput $ws = null,
			IController $controller = null
		)
	{
		$this->model = $model;
		$this->ws = $ws;
		$this->controller = $controller;
	}

	/**
	 * @return IController|null
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

	/**
	 * @return IOutput
	 */
	function getWriteStream()
	{
		return $this->ws;
	}
}

?>