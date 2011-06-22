<?php
/* ***********************************************************************************************
 *
 * Phoebius Framework
 *
 * **********************************************************************************************
 *
 * Copyright (c) 2009 Scand Ltd.
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
 * Invokes a Controller class (that implements IController) with the provided route data
 * 
 * @ingroup Mvc
 */
class MvcDispatcher
{
	function handle(RouteData $routeData, WebRequest $request)
	{
		if (!isset($routeData['controller']))
			return $this->handleError($routeData, $request);
		
		$controllerName = $routeData['controller'];
		$controllerClassName = $this->getControllerClassName($controllerName, $routeData);
		
		if (!class_exists($controllerClassName))
			return $this->handleError($routeData, $request);

		$controllerObject = $this->getControllerInstance($controllerClassName, $routeData);

		$controllerObject->handle($routeData, $request);
	}	

	/**
	 * Gets a new instance of the requested controller
	 *
	 * @param string $controllerClassName name of the class that represents a requested controller
	 *
	 * @return IController
	 */
	protected function getControllerInstance($controllerClassName, RouteData $routeData)
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
	protected function getControllerClassName($controllerName, RouteData $routeData)
	{
		return ucfirst($controllerName) . 'Controller';
	}
	
	private function handleError(RouteData $routeData, WebRequest $request)
	{
		if (!isset($routeData['defaultController']))
			throw new DispatchControllerException($routeData, $request);
		
		$controllerName = $routeData['defaultController'];
		$controllerClassName = $this->getControllerClassName($controllerName, $routeData);
		
		Assert::isTrue(
			class_exists($controllerClassName, true),
			'missing %s as defaultController',
			$controllerClassName
		);

		$controllerObject = $this->getControllerInstance($controllerClassName, $routeData);

		$controllerObject->handle($routeData, $request);
	}
}

?>