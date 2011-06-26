<?php
/* ***********************************************************************************************
 *
 * Phoebius Framework
 *
 * **********************************************************************************************
 *
 * Copyright (c) 2011 Scand Ltd.
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
 * Simple LILO route chain implementation.
 *
 * Example:
 * @code
 * $router->any("/blog:controller/entry:action/?id");
 * @endcode
 *
 * @ingroup App_Web_Routing
 */
class Router implements IRouter
{
	private $routeData = array();
	private $routes = array();
	
	function __construct(array $defaultRouteData = array())
	{
		$this->routeData = $defaultRouteData;
	}
	
	function process(WebRequest $request)
	{
		return $this->lookup($this->routes, $request);
	}
	
	function getDefaultRouteData()
	{
		return $this->routeData;
	}
	
	function get($uri, array $routeData = array())
	{
		$this->routes[] = new Route($uri, $routeData, WebRequestPart::get());
	}
	
	function post($uri, array $routeData = array())
	{
		$this->routes[] = new Route($uri, $routeData, WebRequestPart::post());
	}
	
	function any($uri, array $routeData = array())
	{
		$this->routes[] = new Route($uri, $routeData);
	}
	
	function all(array $routeData)
	{
		$this->routes[] = new Route(null, $routeData);
	}
	
	private function lookup(array $routes, WebRequest $request)
	{
		foreach ($routes as $route) {
			$result = $route->match($request);
			
			if ($result)
				return 
					new RouteData(
						array_merge(
							$this->routeData, 
							$result
						)
					);
		}
		
		if (!empty($this->routeData)) {
			return new RouteData($this->routeData);
		}
		
		throw new RouteException($request->getHttpUrl());
	}
}
