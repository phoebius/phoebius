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
 * @ingroup App_Routing
 */
class RewriteRuleChain implements IRequestRewriteRule
{
	/**
	 * @var array of {@link IRequestRewriteRule}
	 */
	private $rules = array();

	/**
	 * @return RewriteRuleChain
	 */
	static function create(array $rules = array())
	{
		return new self ($rules);
	}

	function __construct(array $rules = array())
	{
		foreach ($rules as $rule)
		{
			$this->addRule($rule);
		}
	}

	/**
	 * @return RewriteRuleChain an object itself
	 */
	function addRule(IRequestRewriteRule $rule)
	{
		$this->rules[] = $rule;

		return $this;
	}

	/**
	 * @return boolean
	 */
	function isMatch(IAppRequest $request)
	{
		foreach ($this->rules as $rule) {
			if (!$rule->isMatch($request)) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Rewrites the specified request to the resulting route specified
	 * @throws RequestRewriteException
	 * @return Route
	 */
	function rewrite(IAppRequest $request, Route $route)
	{
		foreach ($this->rules as $rule) {
			$rule->rewrite($request, $route);
		}

		return $route;
	}

	/**
	 * Casts the specified route to the request
	 * @throws RouteHandleException
	 * @return IAppRequest
	 */
	function compose(Route $route, IAppRequest $request)
	{
		foreach ($this->rules as $rule) {
			$rule->compose($route, $request);
		}

		return $request;
	}
}

?>