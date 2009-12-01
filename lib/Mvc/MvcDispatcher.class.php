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
 * Represents a class that obtains a Trace handler - an IController object - according to
 * the Trace and invokes the object to handle the trace
 *
 * A name of a controller class consists of the value taken of "controller" parameter taken from
 * Trace and "Controller" postfix.
 *
 * A controller MUST implement IController interface.
 *
 * @ingroup Mvc
 */
class MvcDispatcher implements IRouteDispatcher
{
	const PARAMETER_CONTROLLER_NAME = 'controller';

	function handle(Trace $trace)
	{
		$controllerName = $trace->getRequiredParameter(self::PARAMETER_CONTROLLER_NAME);

		$controllerClassName = $this->getControllerClassName($controllerName);

		if (!class_exists($controllerClassName, true)) {
			throw new TraceException(
				sprintf('unknown controller %s', $controllerClassName),
				$trace
			);
		}

		if (
				!in_array('IController', class_implements($controllerClassName, true))
		) {
			throw new TraceException(
				sprintf('%s is not a controller due it does not implement IController', $controllerClassName),
				$trace
			);
		}

		$controllerObject = $this->getControllerInstance($controllerClassName, $trace);

		$controllerObject->handle($trace);
	}

	/**
	 * Gets a new instance of the requested controller
	 *
	 * @param string $controllerClassName name of the class that represents a requested controller
	 * @param Trace $trace trace to handle
	 *
	 * @return IController
	 */
	protected function getControllerInstance($controllerClassName, Trace $trace)
	{
		return new $controllerClassName;
	}

	/**
	 * Gets the name of controller class. This method DOES NOT check the existance of the method.
	 *
	 * By default, this method appends the "Controller" postfix to the value of "controller"
	 * parameter and upercases the first letter
	 *
	 * @param string $controllerName requested controller
	 *
	 * @return string
	 */
	protected function getControllerClassName($controllerName)
	{
		return ucfirst($controllerName) . 'Controller';
	}
}

?>