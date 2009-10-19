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
 * @ingroup Mvc_Exceptions
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