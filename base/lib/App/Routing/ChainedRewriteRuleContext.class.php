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
class ChainedRewriteRuleContext extends RewriteRuleContext
{
	/**
	 * @var IRouteDispatcher
	 */
	private $dispatcher;

	function __construct(
			IRouteDispatcher $dispatcher,
			IRoutingPolicy $policy,
			$name,
			IAppRequest $request
		)
	{
		parent::__construct($policy, $name, $request);

		$this->dispatcher = $dispatcher;
	}

	/**
	 * @return IRouteDispatcher
	 */
	function getRouteDispatcher()
	{
		return $this->dispatcher;
	}

	/**
	 * @return void
	 */
	function handle(Route $route, IAppContext $appContext)
	{
		$this->getRouteDispatcher()->handle(new RouteContext($route, $this), $appContext);
	}
}

?>