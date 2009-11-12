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
 * @ingroup App_Web_Routing_Rules
 */
class RewriteRuleChain implements IRewriteRule
{
	/**
	 * @var array of {@link IRewriteRule}
	 */
	private $rules = array();

	function __construct(array $rules = array())
	{
		foreach ($rules as $rule) {
			$this->addRule($rule);
		}
	}

	/**
	 * @return RewriteRuleChain an object itself
	 */
	function addRule(IRewriteRule $rule)
	{
		$this->rules[] = $rule;

		return $this;
	}

	/**
	 * @return array
	 */
	function rewrite(IWebContext $webContext)
	{
		$yield = array();

		foreach ($this->rules as $rule) {
			$yield = array_merge(
				$yield,
				$rule->rewrite($webContext)
			);
		}

		return $yield;
	}

	/**
	 * @return void
	 */
	function compose(HttpUrl $url, array $parameters)
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
}

?>