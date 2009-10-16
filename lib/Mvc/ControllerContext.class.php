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
 * @ingroup Mvc
 */
class ControllerContext implements IControllerContext
{
	/**
	 * @var IRouteContext
	 */
	private $routeContext;

	/**
	 * @var IAppContext
	 */
	private $appContext;

	function __construct(IRouteContext $routeContext, IAppContext $appContext)
	{
		$this->appContext = $appContext;
		$this->routeContext = $routeContext;
	}

	/**
	 * @return IRouteContext
	 */
	function getRouteContext()
	{
		return $this->routeContext;
	}

	/**
	 * @return IAppContext
	 */
	function getAppContext()
	{
		return $this->appContext;
	}
}

?>