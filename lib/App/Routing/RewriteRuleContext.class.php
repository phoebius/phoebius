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
class RewriteRuleContext implements IRewriteRuleContext
{
	/**
	 * @var IRoutingPolicy
	 */
	private $routingPolicy;

	/**
	 * @var IAppRequest
	 */
	private $request;

	/**
	 * @var string
	 */
	private $name;

	/**
	 * @var IRewriteRule
	 */
	private $rule;

	function __construct(IRoutingPolicy $policy, $name, IAppRequest $request)
	{
		Assert::isScalar($name);

		$this->routingPolicy = $policy;
		$this->name = $name;
		$this->rule = $this->routingPolicy->getRule($name);
		$this->request = $request;
	}

	/**
	 * @return string
	 */
	function getRuleName()
	{
		return $this->name;
	}

	/**
	 * @return IRewriteRule
	 */
	function getRule()
	{
		return $this->rule;
	}

	/**
	 * @return IRoutingPolicy
	 */
	function getRoutingPolicy()
	{
		return $this->routingPolicy;
	}

	/**
	 * @return IAppRequest
	 */
	function getRequest()
	{
		return $this->request;
	}
}

?>