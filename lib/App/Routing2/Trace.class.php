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
 * Trace is a collection of parameters initialized over processing the request parameters
 * @ingroup App_Routing
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
	}

	/**
	 * @return Trace|null
	 */
	function getParentTrace()
	{
		return $this->parentTrace;
	}

	/**
	 * @return Route
	 */
	function getRoute()
	{
		return $this->route;
	}

	/**
	 * @return IRouteTable
	 */
	function getRouteTable()
	{
		return $this->routeTable;
	}

	/**
	 * @return IWebContext
	 */
	function getWebContext()
	{
		return $this->webContext;
	}

	/**
	 * @return void
	 */
	function handle()
	{
		$this->route->getDispacher()->handle($this);
	}

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

	/**
	 * @return Trace
	 */
	function spawn()
	{
		$clone = clone $this;
		$clone->parentTrace = $this;

		return $clone;
	}
}

?>