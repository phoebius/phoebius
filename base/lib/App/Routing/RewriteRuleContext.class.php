<?php
/* ***********************************************************************************************
 *
 * Phoebius Framework
 *
 * **********************************************************************************************
 *
 * Copyright notice
 *
 ************************************************************************************************/

/**
 * @ingroup Routing
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