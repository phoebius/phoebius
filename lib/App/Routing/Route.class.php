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
 * Route is a collection of parameters initialized over processing the request parameters
 * @ingroup App_Routing
 */
class Route extends Collection
{
	/**
	 * @var boolean
	 */
	private $isHandled = false;

	/**
	 * @return boolean
	 */
	function isHandled()
	{
		return $this->isHandled;
	}

	/**
	 * @return Route an object itself
	 */
	function setHandled()
	{
		$this->isHandled = true;

		return $this;
	}
}

?>