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
 * Thrown when {@link IRoutingPolicy::getMatchedRule()} cannot find the corresponding
 * {@link IRequestRewriteRule} to rewrite the request
 * @ingroup App_Routing_Exceptions
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