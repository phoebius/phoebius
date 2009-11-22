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
 * Route is an object that matches an IWebContext using the rules (IRewriteRule objects)
 * producing Trace.
 *
 * Each Route stores an IRouteDispatcher that is allowed to handle the produced Trace.
 *
 * Important to note, that Route is bi-directional: it can convert a matched IWebContext to
 * a Trace and produce valid HttpUrl (as an important parth of IWebContext0 from a Trace.
 * See Route::trace() and Route::compose() methods correspondingly
 *
 * @ingroup App_Web_Routing
 */
class Route
{
	/**
	 * @var IRouteDispatcher
	 */
	private $dispatcher;

	/**
	 * @var array of IRewriteRule
	 */
	private $rules = array();

	/**
	 * @param IRouteDispatcher $dispatcher object that is responsible for handling the Trace that
	 * 			that is produced by the Rouet
	 * @param array $rules array of IRewriteRule that can produce the Trace according to
	 * 			an IWebContext passed in
	 */
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
	 * Tries to match an IWebContext by passing it thru the set or IRewriteRule rules and
	 * produce a Trace.
	 *
	 * @param IRouteTable $routeTable a table of routes
	 * @param IWebContext $webContext context to be iterated over the rules
	 * @param Trace $parentTrace optional Trace which's failure cascaded producing another Trace
	 *
	 * @throws RouteException in case when routing failed because of mismatch of an IWebContext
	 * @return Trace a newly created Trace
	 */
	function trace(IRouteTable $routeTable, IWebContext $webContext, Trace $parentTrace = null)
	{
		$trace = new Trace($this, $routeTable, $webContext, $parentTrace);

		$this->fillTrace($trace, $webContext);

		return $trace;
	}

	/**
	 * Composes a HttpUrl object with parameters produced on matching the IWebContext
	 *
	 * @param HttpUrl $url an object to tune
	 * @param array $parameters parameters to pass to the rules
	 *
	 * @return void
	 */
	function compose(HttpUrl $url, array $parameters = array())
	{
		foreach ($this->rules as $rule) {
			$rule->compose($url, $parameters);
		}
	}

	/**
	 * Gets the list of (required) parameters that are accepted for Route::compose() when
	 * tuning the HttpUrl
	 *
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
	 * Gets the dispatcher object that can handle Trace produced when tracing the Route
	 *
	 * @return IRouteDispatcher
	 */
	function getDispacher()
	{
		return $this->dispatcher;
	}

	/**
	 * @return void
	 */
	private function fillTrace(Trace $trace, IWebContext $webContext)
	{
		foreach ($this->rules as $rule) {
			try {
				$trace->append(
					$rule->rewrite($webContext)
				);
			}
			catch (RewriteException $e) {
				throw new RouteException(
					$e->getMessage(),
					$this,
					$webContext
				);
			}
		}
	}
}

?>