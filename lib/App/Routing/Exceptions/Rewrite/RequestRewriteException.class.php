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
 * Thrown when the invoked {@link IRequestRewriteRule::rewrite()} doesn't match the passed
 * request
 * @ingroup App_Routing_Exceptions
 */
abstract class RequestRewriteException extends StateException
{
	/**
	 * @var IRequestRewriteRule
	 */
	private $rewriteRule;

	/**
	 * @var IAppRequest
	 */
	private $request;

	function __construct(
			IRequestRewriteRule $rule,
			IAppRequest $request,
			$message = 'rule does not match'
		)
	{
		parent::__construct($message);

		$this->rewriteRule = $rule;
		$this->request = $request;
	}

	/**
	 * @return IRequestRewriteRule
	 */
	function getRequestRewriteRule()
	{
		return $this->rewriteRule;
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