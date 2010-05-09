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
 * Represents an controller that maps an "action" parameter from the Trace to the corresponding
 * method of the object, and invokes it to handle the Trace.
 *
 * A method name consists of an "action_" prefix and the value of "action" parameter taken from
 * the Trace.
 *
 * If method is not defined, controller invokes ActionBasedController::handleUnknownAction()
 * which is by default throws TraceException telling that trace is wrong. This method can be
 * reimplemented in descendants and thus gracefully handle failed Trace handling process.
 *
 * Each action method encapsulates business logic and should produce a result - an object that
 * implements IActionResult. The controller provides some useful helper methods for doing this:
 * - ActionBasedController::view() for producing a ViewResult which encapsulates presentation
 * - ActionBasedController::redirect() for producing external redirects; destination is assembled
 * 			automatically according to the Route which is defined in IRouteTable
 *
 *
 * @ingroup Mvc
 */
abstract class ActionBasedController implements IController
{
	const PARAMETER_ACTION = 'action';

	/**
	 * @var Trace|null
	 */
	private $trace;

	/**
	 * @var Model|null
	 */
	private $model;

	function handle(Trace $trace)
	{
		$this->trace = $trace;

		$action = 
			isset($trace[self::PARAMETER_ACTION])
				? $trace[self::PARAMETER_ACTION]
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

		$this->processResult($result);

		$this->trace = null;
	}

	/**
	 * Gets the current trace
	 *
	 * @return Trace
	 */
	function getTrace()
	{
		return $this->trace;
	}

	/**
	 * Gets the current Model to be passed to presentation layer
	 *
	 * @return Model
	 */
	function getModel()
	{
		if (!$this->model) {
			$this->model = new Model();
		}

		return $this->model;
	}

	/**
	 * Gets the actual parameter value to be used when invoking action method
	 *
	 * @param ReflectionParameter $argument
	 *
	 * @throws TraceException thrown in case when value cannot being obtained
	 *
	 * @return mixed
	 */
	protected function filterArgumentValue(ReflectionParameter $argument)
	{
		$request = $this->trace->getWebContext()->getRequest();

		if (isset($request[$argument->name])) {
			$value = $this->getActualParameterValue($argument, $request[$argument->name]);
		}
		else if (isset($this->trace[$argument->name])) {
			$value = $this->getActualParameterValue($argument, $this->trace[$argument->name]);
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
			throw new TraceException(
				sprintf('nothing to pass to %s argument', $argument->name),
				$this->trace
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
		$actionResult->handleResult(
			new ViewContext(
				$this->getModel(),
				$this->trace
			)
		);
	}

	/**
	 * Represents an action method invoked in case when no other action method can be invoked
	 * to handle the Trace.
	 *
	 * By default this method throws TraceException to notify a calling code that no action
	 * method found to handle the Trace
	 *
	 * @param string|null $action name of an action that was used when looking up the action method
	 *
	 * @throws TraceException
	 * @return IActionResult
	 */
	protected function handleUnknownAction($action)
	{
		throw new TraceException(
			sprintf('unknown action `%s`', (string) $action),
			$this->trace
		);
	}

	/**
	 * Look ups for the action method that corresponds the requested action, collects parameter
	 * values, invokes the method and wraps its result, if needed.
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
		if (is_object($actionResult) && $actionResult instanceof IActionResult) {
			return $actionResult;
		}

		if (is_string($actionResult)) {
			return $this->view($actionResult);
		}

		Assert::isUnreachable(
			'unknown actionResult `%s`: %s',
			TypeUtils::getName($actionResult),
			$actionResult
		);
	}

	/**
	 * Helper method that creates an action method result encapsulating presentation object
	 *
	 * @param string $viewName relative path to a view
	 * @param array $data business logic resulting data to be passed to presentation
	 *
	 * @return ViewResult
	 */
	protected function view($viewName, array $data = array())
	{
		$this->getModel()->append($data);

		$presentation = new UIViewPresentation($viewName);
		$presentation->setModel($this->getModel());
		$presentation->setTrace($this->trace);

		return new ViewResult(new UIPage($presentation));
	}

	/**
	 * Helper method that creates an action method result encapsulating redirection
	 *
	 * @param string $routeName name of the route to use when building an address.
	 * 					Route must be presented in IRouteTable
	 * @param array $parameters parameters to pass to Route for building an address
	 *
	 * @return RedirectToRouteResult
	 */
	protected function redirect($routeName, array $parameters = array())
	{
		$url = $this->trace->getWebContext()->getRequest()->getHttpUrl()->spawnBase();

		$this->trace
			->getRouteTable()
			->getRoute($routeName)
			->compose($url, $parameters);

		return new RedirectResult($url);
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