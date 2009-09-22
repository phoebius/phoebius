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
 * @ingroup MvcExceptions
 */
abstract class MvcControllerDispatchException extends MvcHandleException
{
	/**
	 * @var IControllerContext
	 */
	private $controllerContext;

	/**
	 * @var string
	 */
	private $controllerClassName;

	function __construct(IControllerContext $context, $controllerName, $controllerClassName, $message)
	{
		parent::__construct(
			$context,
			MvcDispatcher::PARAMETER_CONTROLLER_NAME,
			$controllerName,
			$message
		);

		$this->controllerContext = $context;
		$this->controllerClassName = $controllerClassName;
	}

	/**
	 * @return $context
	 */
	function getControllerContext()
	{
		return $this->controllerContext;
	}

	/**
	 * @return string
	 */
	function getControllerName()
	{
		return $this->getParameterValue();
	}

	/**
	 * @return string
	 */
	function getControllerClassName()
	{
		return $this->controllerClassName;
	}
}

?>