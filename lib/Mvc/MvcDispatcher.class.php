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
 * @ingroup Mvc
 */
class MvcDispatcher implements IRouteDispatcher
{
	const PARAMETER_CONTROLLER_NAME = 'controller';
	
	/**
	 * @throws TraceException
	 * @return void
	 */
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
	 * @return IController
	 */
	protected function getControllerInstance($controllerClassName, Trace $trace)
	{
		return new $controllerClassName;
	}

	/**
	 * @return string
	 */
	protected function getControllerClassName($controllerName)
	{
		return ucfirst($controllerName) . 'Controller';
	}
}

?>