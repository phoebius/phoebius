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
 * Thrown when {@link IRoutingPolicy::getMatchedRule()} cannot find the corresponding
 * {@link IRequestRewriteRule} to rewrite the request
 * @ingroup RoutingExceptions
 */
class RoutingException extends StateException
{
	/**
	 * @var IRoutingPolicy
	 */
	private $policy;

	/**
	 * @var IAppRequest
	 */
	private $request;

	function __construct(IRoutingPolicy $policy, IAppRequest $request)
	{
		parent::__construct('routing failed: cannot find an approptiate IRequestRewriteRule');

		$this->policy = $policy;
		$this->request = $request;
	}

	/**
	 * @return IRoutingPolicy
	 */
	function getRoutingPolicy()
	{
		return $this->policy;
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