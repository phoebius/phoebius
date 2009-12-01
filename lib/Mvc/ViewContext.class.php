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
 * Represents an execution context of a presentation view
 *
 * @ingroup Mvc
 */
class ViewContext implements IViewContext
{
	/**
	 * @var Model
	 */
	private $model;

	/**
	 * @var Trace
	 */
	private $trace;

	/**
	 * @param Model $model
	 * @param Trace $trace
	 */
	function __construct(Model $model, Trace $trace)
	{
		$this->model = $model;
		$this->trace = $trace;
	}

	function getModel()
	{
		return $this->model;
	}

	function getTrace()
	{
		return $this->trace;
	}

	function getResponse()
	{
		return $this->trace->getWebContext()->getResponse();
	}
}

?>