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
 * Thrown when {@link Route} cannot been handled due it's inconsistency. Thrown by
 * {@link IRouteDispatcher::handle()} and by {@link IRequestRewriteRule::compose()}
 * @ingroup App_Routing_Exceptions
 */
abstract class RouteHandleException extends StateException
{
	/**
	 * @var IRouteContext
	 */
	private $routeContext;

	/**
	 * @param IRequestRewriteRule $rule
	 * @param scalar $message
	 */
	function __construct(IRouteContext $context, $message = 'route is inconsistent')
	{
		parent::__construct($message);

		$this->routeContext = $context;
	}

	/**
	 * @return IRouteContext
	 */
	function getRouteContext()
	{
		return $this->routeContext;
	}
}

?>