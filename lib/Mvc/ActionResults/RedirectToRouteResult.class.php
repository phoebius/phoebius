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
 * Represents a redirection to a route
 * @ingroup Mvc_ActionResults
 */
class RedirectToRouteResult extends RedirectResult
{
	function __construct($routeName, array $parameters = array(), Trace $trace)
	{
		$url = $trace->getWebContext()->getRequest()->getHttpUrl()->spawnBase();

		$trace
			->getRouteTable()
			->getRoute($routeName)
			->compose($url, $parameters);

		parent::__construct($url);
	}
}

?>