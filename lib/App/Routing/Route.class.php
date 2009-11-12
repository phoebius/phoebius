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
class Route
{
	private $urlPattern;

	/**
	 * @var IRouteDispatcher
	 */
	private $dispatcher;

	/**
	 * @var array of {@link IRewriteRule}
	 */
	private $rules = array();

	function __construct(
			IRouteDispatcher $dispatcher,
			array $rules = array()
		)
	{
		$this->dispatcher = $dispatcher;

		foreach ($rules as $rule) {
			$this->addRule($rule);
		}
	}

	/**
	 * @return Trace
	 */
	function from(Trace $trace)
	{
		$webContext = $trace->getWebContext();
		$trace = $trace->spawn();

		$this->fillTrace($trace, $webContext);

		return $trace;
	}

	/**
	 * @throws RouteException
	 * @return Trace
	 */
	function trace(IRouteTable $routeTable, WebContext $webContext)
	{
		$trace = new Trace($this, $routeTable, $webContext);

		$this->fillTrace($trace, $webContext);

		return $trace;
	}

	/**
	 * @return void
	 */
	function compose(HttpUrl $url, array $parameters = array())
	{
		foreach ($this->rules as $rule) {
			$rule->compose($url, $parameters);
		}
	}

	/**
	 * @return array
	 */
	function getParameterList($requiredOnly = true)
	{
		$yield = array();

		foreach ($this->rules as $rule) {
			$yield = array_merge(
				$yield,
				$rule->getParameterList($requiredOnly)
			);
		}

		return $yield;
	}

	/**
	 * @return IRouteDispatcher
	 */
	function getDispacher()
	{
		return $this->dispatcher;
	}

	/**
	 * @return Route
	 */
	protected function addRule(IRewriteRule $rule)
	{
		$this->rules[] = $rule;

		return $this;
	}

	/**
	 * @return void
	 */
	private function fillTrace(Trace $trace, WebContext $webContext)
	{
		foreach ($this->rules as $rule) {
			try {
				$trace->append(
					$rule->rewrite($webContext)
				);
			}
			catch (RewriteException $e) {
				throw new RouteException($e->getMessage());
			}
		}
	}
}

?>