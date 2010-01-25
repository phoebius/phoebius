<?php
/* ***********************************************************************************************
 *
 * Phoebius Framework
 *
 * **********************************************************************************************
 *
 * Copyright (c) 2009 Scand Ltd.
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
 * Represents an interface to the set of named application routes (objects of the Route class).
 *
 * @ingroup App_Web_Routing
 */
interface IRouteTable
{
	/**
	 * Gets the named Route
	 *
	 * @param string $name name of the Route
	 * @throws ArgumentException if named route not found
	 * @return Route
	 */
	function getRoute($name);
}

?>