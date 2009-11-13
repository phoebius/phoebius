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
abstract class ActionBasedController extends Controller
{
	const PARAMETER_ACTION = 'action';
	
	/**
	 * @var Trace|null
	 */
	private $trace;

	/**
	 * @throws RouteHandleException
	 * @return void
	 */
	function handle(Trace $trace)
	{
		$this->trace = $trace;
		
		if (isset($trace[self::PARAMETER_ACTION])) {
			$result = $this->processAction($trace[self::PARAMETER_ACTION]);
		}
		else {
			$result = $this->handleUnknownAction(null);
		}

		$this->processResult($result);
		
		$this->trace = null;
	}
	
	/**
	 * @return Trace|null
	 */
	protected function getCurrentTrace()
	{
		return $this->trace;
	}

	/**
	 * @return mixed
	 */
	protected function filterArgumentValue(ReflectionParameter $argument)
	{
		$request = $this->trace->getWebContext()->getRequest();

		if (isset($request[$argument->name])) {
			$value = $this->getActualVariableValue($argument, $request[$argument->name]);
		}
		else if (isset($this->trace[$argument->name])) {
			$value = $this->getActualVariableValue($argument, $this->trace[$argument->name]);
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
	 * @return array|null
	 */
	protected function getArrayValue(ReflectionParameter $argument, $value)
	{
		if (is_array($value)) {
			return $value;
		}
	}
	
	/**
	 * @return object|null
	 */
	protected function getClassValue(ReflectionClass $class, ReflectionParameter $argument, $value)
	{
		if (is_object($value)) {
			if ($class->isSubclassOf($value)) {
				return $value;
			}
		}
		else if ($class->implementsInterface('IObjectMappable')) {
			try {
				return call_user_func_array(
					array($class->name, 'cast'),
					array($value)
				);	
			}
			catch (TypeCastException $e){}
		}
	}
	
	/**
	 * @return mixed
	 */
	protected function getActualVariableValue(ReflectionParameter $argument, $value)
	{
		if ($argument->isArray()) {
			return $this->getArrayValue($argument, $value);
		}
		else if (($class = $argument->getClass())) {
			return $this->getClassValue($class, $argument, $value);
		}
		else {
			return $value;
		}
	}

	/**
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
	 * Overridden
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
	 * @return IActionResult
	 */
	protected function processAction($action)
	{
		$actionMethod = $this->getMethodName($action);
		$reflectedController = new ReflectionObject($this);

		if ($reflectedController->hasMethod($actionMethod)) {
			$actionResult = $this->invokeActionMethod(
				$reflectedController->getMethod($actionMethod)
			);
		}
		else {
			$actionResult = $this->handleUnknownAction($action);
		}

		$this->trace->setHandled();

		if (
				!(
					is_object($actionResult) && $actionResult instanceof IActionResult
				)
		) {
			$actionResult = $this->makeActionResult($actionResult);
		}

		Assert::isTrue(
			$actionResult instanceof IActionResult,
			'action method can return IActionResult or view name'
		);

		return $actionResult;
	}

	/**
	 * Cast the result (of any type) of method to IActionResult. Now works as a stub ONLY
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
			'unknown actionResult %s: %s',
			get_type($actionResult),
			$actionResult
		);
	}
	
	/**
	 * @return ViewResult
	 */
	protected function view($viewName)
	{
		return new ViewResult(
			UIViewPresentation::view($viewName, $this->getModel())
		);
	}
	
	/**
	 * Overridden
	 * @return string
	 */
	protected function getMethodName($action)
	{
		return 'action_' . ($action);
	}

	/**
	 * @return mixed
	 */
	private function invokeActionMethod(ReflectionMethod $method)
	{
		$argumentsToPass = array();

		foreach ($method->getParameters() as $parameter) {
			$argumentsToPass[$parameter->name] = $this->filterArgumentValue($parameter);
		}
		
		$actionMethodResult = $method->invokeArgs($this, $argumentsToPass);

		return $actionMethodResult;
	}
}

?>