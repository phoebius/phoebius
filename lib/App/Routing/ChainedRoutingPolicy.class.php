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
 * TODO: make rule naming optional
 * @ingroup App_Routing
 */
class ChainedRoutingPolicy implements IRoutingPolicy
{
	/**
	 * @var array of {@link IRequestRewriteRule}
	 */
	private $rules = array();

	/**
	 * @var array of {@link IRouteDispatcher}
	 */
	private $dispatchers = array();

	/**
	 * @var IRouteDispatcher|null
	 */
	private $defaultDispatcher = null;

	/**
	 * @return ChainedRoutingPolicy
	 */
	static function create()
	{
		return new self;
	}

	/**
	 * @return ChainedRoutingPolicy an object itself
	 */
	function setDefaultDispatcher(IRouteDispatcher $dispatcher)
	{
		$this->defaultDispatcher = $dispatcher;

		return $this;
	}

	/**
	 * @return IRouteDispatcher|null
	 */
	function getDefaultDispatcher()
	{
		return $this->defaultDispatcher;
	}

	/**
	 * @return ChainedRoutingPolicy an object itself
	 */
	function addRule($name, IRequestRewriteRule $rule, IRouteDispatcher $dispatcher = null)
	{
		$this->appendRule($name, $rule, $dispatcher);

		return $this;
	}

	/**
	 * @return ChainedRoutingPolicy an object itself
	 */
	function addRules(array $rules, IRouteDispatcher $dispatcher = null)
	{
		foreach ($rules as $name => $rule) {
			$this->addRule($name, $rule, $dispatcher);
		}

		return $this;
	}

	/**
	 * @return ChainedRoutingPolicy
	 */
	function appendRule($name, IRequestRewriteRule $rule, IRouteDispatcher $dispatcher = null)
	{
		Assert::isScalar($name);
		Assert::isFalse(isset($this->rules[$name]), 'ule `%s` already defined', $name);

		$this->rules[$name] = $rule;

		if ($dispatcher) {
			$this->dispatchers[$name] = $dispatcher;
		}

		return $this;
	}

	/**
	 * @return ChainedRoutingPolicy
	 */
	function prependRule($name, IRequestRewriteRule $rule, IRouteDispatcher $dispatcher = null)
	{
		Assert::isScalar($name);
		Assert::isFalse(isset($this->rules[$name]), "rule `{$name}` already defined");

		$this->rules = array($name => $rule) + $this->rules;

		if ($dispatcher)
		{
			$this->dispatchers[$name] = $dispatcher;
		}

		return $this;
	}

	/**
	 * @throws ArgumentException
	 * @return IRequestRewriteRule
	 */
	function getRule($name)
	{
		if (!isset($this->rules[$name])) {
			throw new ArgumentException('name', 'rule not defined');
		}

		return $this->rules[$name];
	}

	/**
	 * @return IRequestRewriteRule
	 */
	function setRuleDispatcher($name, IRouteDispatcher $dispatcher)
	{
		Assert::isTrue(isset($this->rules[$name]), 'no such rule');

		$this->dispatchers[$name] = $dispatcher;

		return $this;
	}

	/**
	 * @return IRouteDispatcher
	 */
	function getRuleDispacher($name)
	{
		Assert::isTrue(isset($this->rules[$name]), 'no such rule');

		return isset($this->dispatchers[$name])
			? $this->dispatchers[$name]
			: $this->defaultDispatcher;
	}

	/**
	 * @return array of {@link IRequestRewriteRule}
	 */
	function getRules()
	{
		return $this->rules;
	}

	/**
	 * @throws RoutingException
	 * @return ChainedRewriteRuleContext
	 */
	function getMatchedRuleContext(IAppRequest $request)
	{
		foreach ($this->rules as $name => $rule) {
			if ($rule->isMatch($request)) {
				return $this->getRuleContext($name, $request);
			}
		}

		throw new RoutingException($this, $request);
	}

	/**
	 * @return ChainedRewriteRuleContext
	 */
	function getRuleContext($name, IAppRequest $request)
	{
		$dispatcher = isset($this->dispatchers[$name])
			? $this->dispatchers[$name]
			: $this->defaultDispatcher;

		Assert::isNotNull($dispatcher, 'default dispatcher is not yet set');

		return new ChainedRewriteRuleContext($dispatcher, $this, $name, $request);
	}

	function route(Route $route, IAppContext $appContext)
	{
		$ruleContext = $this->getMatchedRuleContext($appContext->getRequest());
		$ruleContext->handle(
			$ruleContext->getRule()->rewrite($appContext->getRequest(), $route),
			$appContext
		);
	}

	function routeRule($name, Route $route, IAppContext $appContext)
	{
		$ruleContext = $this->getRuleContext($name, $appContext->getRequest());
		$ruleContext->handle(
			$ruleContext->getRule()->rewrite($appContext->getRequest(), $route),
			$appContext
		);
	}
}

?>