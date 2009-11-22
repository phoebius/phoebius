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
 * Represents a collection of parameters produced as the result of routing an IWebContext thru
 * the corresponding Route.
 *
 * The algorithm is the following:
 * - IWebContext is passed to IRouter
 * - IRouter lookups the IRouteTable trying to route IWebContext thru every Route
 * - Route iterates an IWebContext thru the set of IRewriteRules that analyze an IWebContext and fill the Trace with parameters
 * - when routing is successfull and produces a valid Trace, Route marked as matched and the Trace can be handled
 * - Trace can be handled by passing to IRouteDispatcher from the matched Route
 * - if IRouteDispatcher throws TraceException, a special fallback Route is invoked to match an IWebContext
 * - if IRouteDispatcher throws Exception, an application faults due the uncauth exception, and this should be handled
 * 		manually as internal server error
 *
 * @ingroup App_Web_Routing
 */
final class Trace extends Collection
{
	/**
	 * @var boolean
	 */
	private $isHandled = false;

	/**
	 * @var Route
	 */
	private $route;

	/**
	 * @var IRouteTable
	 */
	private $routeTable;

	/**
	 * @var IWebContext
	 */
	private $webContext;

	/**
	 * @var Trace
	 */
	private $parentTrace;

	/**
	 * @param Route $route a Route thru which the IWebContext was passed
	 * @param IRouteTable $routeTable table of routes that was looked up to find the appropriate Route
	 * @param IWebContext $webContext context that was passed thru the Route
	 * @param Trace $parentTrace inital Trace which's handling failed cascading usage of a fallback Route
	 */
	function __construct(
			Route $route,
			IRouteTable $routeTable,
			IWebContext $webContext,
			Trace $parentTrace = null
		)
	{
		$this->route = $route;
		$this->routeTable = $routeTable;
		$this->webContext = $webContext;
		$this->parentTrace = $parentTrace;

		parent::__construct();
	}

	/**
	 * Gets the initial Trace which's handling failed cascading usage of a fallback Route
	 *
	 * @return Trace|null
	 */
	function getParentTrace()
	{
		return $this->parentTrace;
	}

	/**
	 * Gets the Route thru which the IWebContext was passed to produce the Trace object
	 *
	 * @return Route
	 */
	function getRoute()
	{
		return $this->route;
	}

	/**
	 * Gets the table of routes that was looked up to find the appropriate Route
	 *
	 * @return IRouteTable
	 */
	function getRouteTable()
	{
		return $this->routeTable;
	}

	/**
	 * Gets the IWebContext that was passed thru the Route to produce the Trace object
	 *
	 * @return IWebContext
	 */
	function getWebContext()
	{
		return $this->webContext;
	}

	/**
	 * Handles the Trace
	 *
	 * @return void
	 */
	function handle()
	{
		$this->route->getDispacher()->handle($this);

		$this->setHandled();
	}

	/**
	 * Determines whether the Trace was successfully handled
	 *
	 * @return boolean
	 */
	function isHandled()
	{
		return $this->isHandled;
	}

	/**
	 * Creates a clean copy of the Trace with the current Trace object as parent trace
	 *
	 * @return Trace
	 */
	function spawnNested()
	{
		$clone = clone $this;
		$clone->parentTrace = $this;
		$clone->erase();

		return $clone;
	}

	/**
	 * Gets the required Trace parameter
	 *
	 * @throws TraceException in case when parameter is not defined within the Trace
	 * @return mixed parameter value
	 */
	function getRequiredParameter($parameter)
	{
		Assert::isScalar($parameter);

		if (!$this->has($parameter)) {
			throw new TraceException(
				sprintf('missing required parameter %s', $parameter),
				$this
			);
		}

		return $this->get($parameter);
	}
}

?>