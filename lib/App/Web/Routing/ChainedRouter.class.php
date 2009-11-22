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
 * LIFO route chain implementation.
 *
 * Aggregates routes and lookups the first route that matches the request without an explicit
 * RouteException.
 *
 * Use ChainedRouter::route() for filling the chain with your own common application-wide routes.
 *
 * Example:
 * @code
 * $router->route("blogEntry", "/blog:controller/entry:action/?id");
 * @endcode
 *
 * @ingroup App_Web_Routing
 */
class ChainedRouter implements IRouteTable, IRouter
{
	/**
	 * @var array of name=>Route
	 */
	private $routes = array();

	/**
	 * @var Route
	 */
	private $fallbackRoute;

	/**
	 * @var array of Route
	 */
	private $chain = array();

	/**
	 * @var IRouteDispatcher
	 */
	private $defaultDispatcher;

	/**
	 * @param IRouteDispatcher $dispatcher a default dispatcher for routes
	 */
	function __construct(IRouteDispatcher $dispatcher)
	{
		$this->defaultDispatcher = $dispatcher;
	}

	/**
	 * Gets the default dispatcher for newly created routes
	 *
	 * @return IRouteDispatcher
	 */
	function getDefaultDispatcher()
	{
		return $this->defaultDispatcher;
	}

	/**
	 * Smart route assembler.
	 *
	 * Accepts the name of the route to be assembled, URI to be used as a template for the rules.
	 *
	 * Example:
	 * @code
	 * $router->route("blogEntry", "/blog:controller/entry:action/?id");
	 * @endcode
	 *
	 * In future, the third parameter will be allowed to contain various objects that will be
	 * smartly mapped to appropriate rules.
	 *
	 * @param string $name name of the route
	 * @param string $uri URI that will be used as request variables template
	 * @param array $parameters array of parameters to be appended to Trace
	 * @return ChainedRouter itself
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

	function getTrace(IWebContext $webContext)
	{
		$trace = null;

		foreach ($this->chain as $route) {
			try {
				$trace = $route->trace($this, $webContext);
				break;
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

	function getFallbackTrace(Trace $parentTrace)
	{
		Assert::isNotEmpty($this->fallbackRoute, 'fallback route not found');

		return $this->fallbackRoute->trace($this, $parentTrace->getWebContext(), $parentTrace);
	}

	/**
	 * Appends the Route to the chain
	 *
	 * @param string $name name of the Route to append
	 * @param Route $route route object to append
	 *
	 * @return ChainedRouter itself
	 */
	function addRoute($name, Route $route)
	{
		$this->appendRoute($name, $route);

		return $this;
	}

	/**
	 * Sets the fallback Route.
	 *
	 * Fallback Route will be used to handle situation when no Route matched an IWebContext
	 *
	 * @return ChainedRouter itself
	 */
	function setFallbackRoute(Route $route)
	{
		$this->fallbackRoute = $route;

		return $this;
	}

	/**
	 * Appends the set of named routes to the chain.
	 *
	 * @param array $routes an associative array of Route object, where key is the name of the Route
	 *
	 * @return ChainedRouter itself
	 */
	function addRoutes(array $routes)
	{
		foreach ($routes as $name => $route) {
			$this->addRoute($name, $route);
		}

		return $this;
	}

	/**
	 * Appends the Route to the chain
	 *
	 * @param string $name name of the Route to append
	 * @param Route $route route object to append
	 *
	 * @return ChainedRouter itself
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
	 * Prepends the Route to the chain
	 *
	 * @param string $name name of the Route to append
	 * @param Route $route route object to append
	 *
	 * @return ChainedRouter itself
	 */
	function prependRoute($name, Route $route)
	{
		Assert::isScalar($name);
		Assert::isFalse(isset($this->routes[$name]), 'route `%s` already defined', $name);

		$this->routes[$name] = $route;
		array_unshift($this->chain, $route);

		return $this;
	}

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