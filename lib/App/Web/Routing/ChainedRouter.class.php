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
 * @ingroup App_Web_Routing
 */
class ChainedRouter implements IRouteTable
{
	/**
	 * @var array of name => {@link Route}
	 */
	private $routes = array();

	/**
	 * @var Route
	 */
	private $fallbackRoute;

	/**
	 * @var array of {@link Route}
	 */
	private $chain = array();

	/**
	 * @var IRouteDispatcher|null
	 */
	private $defaultDispatcher = null;

	function __construct(IRouteDispatcher $dispatcher)
	{
		$this->defaultDispatcher = $dispatcher;
	}
	
	/**
	 * @return IRouteDispatcher
	 */
	function getDefaultDispatcher()
	{
		return $this->defaultDispatcher;
	}
	
	/**
	 * Smart route assembler.
	 * Accepts the name of the route to be assembled, URI to be used as a template for the rules.
	 * Example:
	 * <code>
	 * $this->route("blogEntry", "/blog:controller/entry:action/?id");
	 * </code>
	 *
	 * In future, the third parameter will be allowed to contain various objects that will be
	 * smartly mapped to appropriate rules.
	 *
	 * @param string $name name of the route
	 * @param string $uri URI that will be used as request variables template
	 * @param array $parameters array of parameters to be appended to Trace
	 * @return ChainedRouter
	 */
	function route($name, $uri, array $parameters = array())
	{
		Assert::isScalar($name);
		Assert::isScalar($uri);
		
		$rules = array();
		
		$parsedUrlPattern = parse_url($uri);
		
		if (isset($parsedUrlPattern['path'])) {
			$rules[] = new PathRewriteRule($parsedUrlPattern['path']);
		}
		
		if (isset($parsedUrlPattern['query'])) {
			$queryStringVariables = array();
			parse_str($parsedUrlPattern['query'], $queryStringVariables);
			
			foreach ($queryStringVariables as $qsVar => $qsValue) {
				$rules[] = new RequestVarImportRule(
					$qsVar,
					new WebRequestPart(WebRequestPart::GET),
					!empty($qsValue),
					empty($qsValue) ? null : $qsValue
				);
			}
		}
		
		foreach ($parameters as $parameter => $value) {
			$rules[] = new ParameterImportRule($parameter, $value);
		}
		
		$this->addRoute($name, new Route($this->defaultDispatcher, $rules));
		
		return $this;
	}
	
	/**
	 * @return Trace|null
	 */
	function getTrace(IWebContext $webContext)
	{
		$trace = null;

		foreach ($this->chain as $route) {
			try {
				$trace = $route->trace($this, $webContext);
			}
			catch (RouteException $e) {
				//FIXME log failure here
			}
		}

		if (!$trace && $this->fallbackRoute) {
			$trace = $this->fallbackRoute->trace($this, $webContext);
		}

		return $trace;
	}
	
	/**
	 * @return Trace
	 */
	function getFallbackTrace(Trace $parentTrace)
	{
		Assert::isNotEmpty($this->fallbackRoute, 'fallback route not found');

		return $this->fallbackRoute->from($parentTrace);
	}

	/**
	 * @return ChainedRouter an object itself
	 */
	function addRoute($name, Route $route)
	{
		$this->appendRoute($name, $route);

		return $this;
	}

	/**
	 * @return ChainedRouter
	 */
	function setFallbackRoute(Route $route)
	{
		$this->fallbackRoute = $route;

		return $this;
	}

	/**
	 * @return ChainedRouter an object itself
	 */
	function addRoutes(array $routes)
	{
		foreach ($routes as $name => $route) {
			$this->addRoute($name, $route);
		}

		return $this;
	}

	/**
	 * @return ChainedRouter
	 */
	function appendRoute($name, Route $route)
	{
		Assert::isScalar($name);
		Assert::isFalse(isset($this->routes[$name]), 'route `%s` already defined', $name);

		$this->routes[$name] = $route;
		$this->chain[] = $route;

		return $this;
	}

	/**
	 * @return ChainedRouter
	 */
	function prependRoute($name, Route $route)
	{
		Assert::isScalar($name);
		Assert::isFalse(isset($this->routes[$name]), 'route `%s` already defined', $name);

		$this->routes[$name] = $route;
		array_unshift($this->chain, $route);

		return $this;
	}

	/**
	 * @throws ArgumentException
	 * @return Route
	 */
	function getRoute($name)
	{
		Assert::isScalar($name);

		if (!isset($this->routes[$name])) {
			throw new ArgumentException('name', 'route not defined');
		}

		return $this->routes[$name];
	}
}

?>