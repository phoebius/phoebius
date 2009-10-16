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
 * Thrown when the invoked {@link IRequestRewriteRule::rewrite()} doesn't match the passed
 * request
 * @ingroup RewriteRoutingExceptions
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