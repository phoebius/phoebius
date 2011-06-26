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
 * Represents a controller that maps an "action" parameter from the route data to the corresponding
 * method of the object, and invokes it.
 *
 * A method name consists of an "action_" prefix and the value of "action" parameter.
 *
 * If method is not defined, controller invokes ActionBasedController::handleUnknownAction()
 * which is by default throws RouteException.
 *
 * To overload a basic logic of executing a method that corresponds an action, just overload
 * ActionBasedController::processAction() which actually invokes the found method.
 *
 * @ingroup Mvc
 */
abstract class ActionBasedController implements IController
{
	const ROUTE_DATA_ACTION = 'action';
	
	/**
	 * @var RouteData
	 */
	private $routeData;
	
	/**
	 * @var WebRequest
	 */
	private $request;
	
	/**
	 * @return WebRequest
	 */
	protected function getRequest()
	{
		$this->request;
	}

	function handle(RouteData $routeData, WebRequest $request)
	{
		$this->routeData = $routeData;
		$this->request = $request;

		$action = 
			isset($this->routeData[self::ROUTE_DATA_ACTION])
				? $this->routeData[self::ROUTE_DATA_ACTION]
				: null;

		$actionMethod = $this->getMethodName($action);
		$reflectedController = new ReflectionObject($this);

		if ($action && $reflectedController->hasMethod($actionMethod)) {
			$result = $this->processAction($action, $reflectedController->getMethod($actionMethod));
		}
		else {
			$result = $this->handleUnknownAction($action);
		}

		$result = $this->makeActionResult($result);

		if ($result)
			$this->processResult($result);
	}

	/**
	 * Gets the actual parameter value to be used when invoking action method
	 *
	 * @param ReflectionParameter $argument
	 *
	 * @return mixed
	 */
	protected function filterArgumentValue(ReflectionParameter $argument)
	{
		if (isset($this->request[$argument->name])) {
			$value = $this->getActualParameterValue($argument, $this->request[$argument->name]);
		}
		else if (isset($this->routeData[$argument->name])) {
			$value = $this->getActualParameterValue($argument, $this->routeData[$argument->name]);
		}
		else {
			$value = null;
		}

		if (!is_null($value)) {
			return $value;
		}

		// check whether it is optional or have the default value
		if ($argument->allowsNull()) {
			return null;
		}
		elseif ($argument->isDefaultValueAvailable()) {
			return $argument->getDefaultValue();
		}
		elseif ($argument->isArray()) {
			return array();
		}
		else {
			Assert::isUnreachable(
				'nothing to pass to %s argument', $argument->name
			);
		}
	}

	/**
	 * Casts the parameter value which is expected to be an array according to
	 * action method signature
	 *
	 * @param string $action requested action name
	 * @param string $name name of the parameter
	 * @param mixed $value obtained value
	 *
	 * @return array|null
	 */
	protected function getArrayValue($action, $name, $value)
	{
		if (is_array($value)) {
			return $value;
		}
	}

	/**
	 * Casts the parameter value which is expected to be an instance of a class according to action
	 * method signature
	 *
	 * @param string $action requested action name
	 * @param string $name name of the parameter
	 * @param ReflectionClass $class
	 * @param mixed $value obtained value
	 *
	 * @return object|null
	 */
	protected function getClassValue($action, $name, ReflectionClass $class, $value)
	{
		if (is_object($value)) {
			if ($class->isSubclassOf($value)) {
				return $value;
			}
		}
		else if ($class->implementsInterface('IObjectCastable')) {
			try {
				return call_user_func_array(
					array($class->name, 'cast'),
					array($value)
				);
			}
			catch (TypeCastException $e){}
		}
		else if ($class->implementsInterface('IDaoRelated') && is_scalar($value)) {
			try {
				$dao = call_user_func(array($class->name, 'dao'));
				return $dao->getEntityById($value);
			}
			catch (OrmEntityNotFoundException $e){}
		}
	}

	/**
	 * Casts the obtained value to the type expected in action method signature.
	 *
	 * This is low-level method. Consider reimplementing
	 * ActionBasedController::getClassValue() and
	 * ActionBasedController::getArrayValue()
	 *
	 * @param ReflectionParameter $argument
	 * @param mixed $value
	 *
	 * @return mixed
	 */
	protected function getActualParameterValue(ReflectionParameter $argument, $value)
	{
		if ($argument->isArray()) {
			return $this->getArrayValue($this->action, $argument->name, $value);
		}
		else if (($class = $argument->getClass())) {
			return $this->getClassValue($this->action, $argument->name, $class, $value);
		}
		else {
			return $value;
		}
	}

	/**
	 * Runs the proccess of handing the action method result
	 *
	 * @param IActionResult $actionResult
	 *
	 * @return void
	 */
	protected function processResult(IActionResult $actionResult)
	{
		$actionResult->handleResult($this->request->getResponse());
	}

	/**
	 * Represents an action method invoked in case when no other action method can be invoked
	 * to handle request
	 *
	 * By default this method throws DispatchActionException to notify a calling code that no action
	 * method found to handle request
	 *
	 * @param string|null $action name of an action that was used when looking up the action method
	 *
	 * @throws DispatchActionException
	 * @return IActionResult
	 */
	protected function handleUnknownAction($action)
	{
		throw new DispatchActionException($this->routeData, $this->request);
	}

	/**
	 * Collects arguments to be passed to the found action method and invokes it returning its result.
	 *
	 * @param string $action requested action
	 * @param ReflectionMethod $method a method that corresponds the action
	 *
	 * @return mixed result that may be processed by ActionBasedController::makeActionResult()
	 */
	protected function processAction($action, ReflectionMethod $method)
	{
		$argumentsToPass = array();

		foreach ($method->getParameters() as $parameter) {
			$argumentsToPass[$parameter->name] = $this->filterArgumentValue($parameter);
		}

		$actionResult = $method->invokeArgs($this, $argumentsToPass);

		return $actionResult;
	}

	/**
	 * Cast the result of action method (of any type) to IActionResult.
	 *
	 * The following types are supported:
	 * - string is treated as path to a view and wrapped with ViewResult
	 * 		See ActionBasedController::view()
	 * - IActionResult object is treated as-is
	 *
	 * @return IActionResult
	 */
	protected function makeActionResult($actionResult)
	{
		if ($actionResult instanceof IActionResult) {
			return $actionResult;
		}
		
		if ($actionResult instanceof View) {
			return new ViewResult($actionResult);
		}
	}

	/**
	 * Gets the name of action method. This method DOES NOT check the existance of the method
	 *
	 * @param string $action requested action
	 *
	 * @return string
	 */
	protected function getMethodName($action)
	{
		return 'action_' . ($action);
	}
}

?>