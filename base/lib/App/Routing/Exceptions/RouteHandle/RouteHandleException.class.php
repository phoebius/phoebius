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
 * Thrown when {@link Route} cannot been handled due it's inconsistency. Thrown by
 * {@link IRouteDispatcher::handle()} and by {@link IRequestRewriteRule::compose()}
 * @ingroup RouteHandleExceptions
 */
abstract class RouteHandleException extends StateException
{
	/**
	 * @var IRouteContext
	 */
	private $routeContext;

	/**
	 * @param IRequestRewriteRule $rule
	 * @param scalar $message
	 */
	function __construct(IRouteContext $context, $message = 'route is inconsistent')
	{
		parent::__construct($message);

		$this->routeContext = $context;
	}

	/**
	 * @return IRouteContext
	 */
	function getRouteContext()
	{
		return $this->routeContext;
	}
}

?>