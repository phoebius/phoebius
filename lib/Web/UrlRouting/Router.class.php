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
	private $httpMethodRoutes = array();
	private $anyRoutes = array();
	
	function __construct(array $defaultRouteData = array())
	{
		$this->routeData = $defaultRouteData;
	}
	
	function process(WebRequest $request)
	{
		$method = $request->getRequestMethod()->getValue();
		
		if (isset($this->httpMethodRoutes[$method]))
			try {
				return $this->lookup($this->httpMethodRoutes[$method], $request);
			}
			catch (RouteException $e) {};
			
		return $this->lookup($this->anyRoutes, $request);
	}
	
	function getDefaultRouteData()
	{
		return $this->routeData;
	}
	
	function get($uri, array $routeData = array())
	{
		$this->method('GET', $uri, $routeData);
	}
	
	function post($uri, array $routeData = array())
	{
		$this->method('POST', $uri, $routeData);
	}
	
	function method($method, $uri, array $routeData = array())
	{
		if (!isset($this->httpMethodRoutes[$method]))
			$this->httpMethodRoutes[$method] = array();
			
		$this->httpMethodRoutes[$method][] = new Route($uri, $routeData);
	}
	
	function any($uri, array $routeData = array())
	{
		$this->anyRoutes[] = new Route($uri, $routeData);
	}
	
	function all(array $routeData)
	{
		$this->anyRoutes[] = new Route(null, $routeData);
	}
	
	private function lookup(array $routes, WebRequest $request)
	{
		$httpUrl = $request->getHttpUrl();
		
		foreach ($routes as $route) {
			$result = $route->match($httpUrl);
			
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
		
		throw new RouteException($httpUrl);
	}
}
