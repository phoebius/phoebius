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
	 * @var Model
	 */
	private $model;
	
	/**
	 * @var Trace
	 */
	private $trace;

	function __construct(
			Model $model,
			Trace $trace
		)
	{
		$this->model = $model;
		$this->trace = $trace;
	}

	/**
	 * @return Model
	 */
	function getModel()
	{
		return $this->model;
	}
	
	/**
	 * @return Trace
	 */
	function getTrace()
	{
		return $this->trace;
	}
}

?>