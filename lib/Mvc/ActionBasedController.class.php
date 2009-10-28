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
 * TODO: add the following helper methods to generate {@link IActionResult}:
 * View – Returns a ViewResult action result.
 * Redirect – Returns a RedirectResult action result.
 * RedirectToAction – Returns a RedirectToRouteResult action result.
 * RedirectToRoute – Returns a RedirectToRouteResult action result.
 * Json – Returns a JsonResult action result.
 * Content – Returns a ContentResult action result.
 * FIXME: reimplement map system, map types withing routing
 * @ingroup Mvc
 */
abstract class ActionBasedController extends Controller
{
	const PARAMETER_ACTION = 'action';

	/**
	 * @var IControllerContext|null
	 */
	private $currentControllerContext = null;

	/**
	 * @var array
	 */
	private $argumentFilters = array();

	/**
	 * Overridden.
	 * @return IPhpViewDispatcher
	 */
	function getPhpViewDispatcher()
	{
		return new SimplePhpViewDispatcher();
	}

	/**
	 * @throws RouteHandleException
	 * @return void
	 */
	function handle(IControllerContext $context)
	{
		try {
			$action = $context->getRouteContext()->getRoute()->getParameter(
				self::PARAMETER_ACTION,
				$context->getAppContext()->getRequest()
			);
		}
		catch (ArgumentException $e) {
			throw new ParameterMissingException(
				$context->getRouteContext(),
				$this->getActionParameterName()
			);
		}

		$this->currentControllerContext = $context;

		$result = $this->processAction($action, $context);
		$this->processResult($result, $context);

		$this->currentControllerContext = null;
	}

	private $filterArgumentCallbackArgs = array();

	/**
	 * Filter for target method (aka action) argument.
	 *
	 * Arguments passed to the filter:
	 *  * argument name
	 *  * argument type hint (if presented)
	 *  * argument default value (taken from the method definition)
	 *  * IControllerContext
	 *
	 * Expected filter invokation logic:
	 *  * mixed value - treated as smth to be passed as an argument
	 *
	 * @return ActionBasedController
	 */
	function addArgumentFilter($argumentName, $callbackOrValue, array $argumentToPass = array())
	{
		$this->argumentFilters[$argumentName] = $callbackOrValue;
		$this->filterArgumentCallbackArgs[$argumentName] = $argumentToPass;

		return $this;
	}

	protected function processResult(IActionResult $actionResult, IControllerContext $context)
	{
		$actionResult->handleResult(
			new ViewContext(
				$this,
				$this->getModel()
			)
		);
	}

	/**
	 * @return IControllerContext|null
	 */
	function getContext()
	{
		return $this->currentControllerContext;
	}

	/**
	 * @return IControllerContext|null
	 */
	protected function getCurrentControllerContext()
	{
		return $this->currentControllerContext;
	}

	/**
	 * Overridden
	 * @throws RouteHandleException
	 * @return IActionResult
	 */
	function handleUnknownAction($action, IControllerContext $context)
	{
		throw new ParameterTypeException(
			$context->getRouteContext(),
			self::PARAMETER_ACTION,
			$action,
			'unknown action'
		);
	}

	/**
	 * @return IActionResult
	 */
	protected function processAction($action, IControllerContext $context)
	{
		$targetActionMethod = $this->getTargetMethodName($action);
		$reflectedController = new ReflectionObject($this);

		if ($reflectedController->hasMethod($targetActionMethod)) {
			$actionResult = $this->invokeTargetActionMethod(
				$reflectedController->getMethod($targetActionMethod),
				$context
			);
		}
		else {
			$actionResult = $this->handleUnknownAction($action, $context);
		}

		$context->getRouteContext()->getRoute()->setHandled();

		$actionResult = $this->processActionResult($actionResult);

		Assert::isTrue(
			$actionResult instanceof IActionResult,
			'target action method can return IActionResult, scalar or array'
		);

		return $actionResult;
	}

	/**
	 * Cast the result (of any type) of target method to IActionResult. Now works as a stub ONLY
	 * @return IActionResult
	 */
	protected function processActionResult($actionResult)
	{

//		if (empty($actionResult))
//		{
//			$actionResult = $this->view($action);
//		}
//
//		if (is_array($actionResult))
//		{
//			$actionResult = $this->view($action, $actionResult);
//		}
//
//		if (is_scalar($actionResult))
//		{
//			$actionResult = ContentResult::create()->setContent($actionResult);
//		}

		if (is_object($actionResult) && $actionResult instanceof IActionResult) {
			return $actionResult;
		}

		if (is_string($actionResult)) {
			return new ViewResult(UIViewPresentation::view($actionResult, $this->getModel()));
		}
	}

	/**
	 * Overridden
	 * @return string
	 */
	protected function getTargetMethodName($action)
	{
		return 'action_' . ($action);
	}

	/**
	 * @return IActionResult
	 */
	private function invokeTargetActionMethod(
			ReflectionMethod $method,
			IControllerContext $context
		)
	{
		$methodParameters = $method->getParameters();
		$argumentsToPass = array();

		if (!empty($methodParameters)) {
			$argumentsToPass = $this->getTargetMethodArguments($methodParameters, $context);
		}

		$targetActionMethodResult = $method->invokeArgs($this, $argumentsToPass);

		return $targetActionMethodResult;
	}

	/**
	 * FIXME: cut out mapping functionality to a separate rewriting classes
	 * @return array
	 */
	private function getTargetMethodArguments(array $parameters, IControllerContext $context)
	{
		$arguments = array();
		foreach ($parameters as $parameter) {
			$argument = null;
			$name = $parameter->getName();

			Assert::isFalse(
				$parameter->isPassedByReference(),
				'%s argument cannot be passed by reference',
				$name
			);

			if (isset($this->argumentFilters[$name])) {
				$filter = $this->argumentFilters[$name];

				if (is_callable($filter)) {
					$argsToPass = array_merge(
						array (
							$name,
							(!is_null($class = $parameter->getClass()))
								? $class->getName()
								: null,
							$parameter->isDefaultValueAvailable()
								? $parameter->getDefaultValue()
								: null,
							$context
						),
						$this->filterArgumentCallbackArgs[$name]
					);

					$argument = call_user_func_array($filter, $argsToPass);
				}
				else {
					$argument = $filter;
				}
			}
			else {
				try {
					$argument = $context->getRouteContext()->getRoute()->getParameter(
						$parameter->getName(),
						$context->getAppContext()->getRequest()
					);

					if ($parameter->isArray()) {
						if (!is_array($argument)) {
							if ($parameter->allowsNull() && !is_null($argument)) {
								throw new ParameterTypeException(
									$context->getRouteContext(),
									$parameter->getName(),
									$argument,
									'array expected as defined in target action method'
								);
							}
						}
					}
					else if (!is_null($class = $parameter->getClass())) {

						if (
								!( // isInstance accepts only objects (stdclass)
									   is_object($argument)
									&& $class->isInstance($argument)
								)
						) {
							if ($class->implementsInterface('IObjectMappable')) {
								try {
									$argument = call_user_func_array(
										array($class->getName(), 'cast'),
										array($argument)
									);
								}
								catch (TypeCastException $e) {
									throw new MvcActionHandleException(
										$context,
										$name,
										$argument,
										'cannot cast value to ' . $class->getName()
									);
								}
							}
							else if ($class->implementsInterface('IOrmRelated') && !!$argument) {

								$ormClass = call_user_func(array($class->getName(), 'orm'));
								$rawValueSet = array();

								if (is_array($argument)) {
									$rawValueSet = array();

									foreach ($this->logicalSchema->getProperties() as $propertyName => $property) {
										foreach ($property->getDBFields() as $columnName) {
											if (array_key_exists($columnName, $argument)) {
												$rawValueSet[$propertyName][] = $argument[$columnName];
											}
											else {
												unset ($rawValueSet[$propertyName]);
												continue(2);
											}
										}
									}
								}
								else if ($class->implementsInterface('IDaoRelated')) {
									$rawValueSet[$ormClass->getIdentifier()->getName()][] = $argument;
								}

								if ($class->implementsInterface('IDaoRelated')) {
									$dao = call_user_func(array($class->getName(), 'dao'));

									$idProperty = $ormClass->getIdentifier();

									if (
											isset($rawValueSet[$idProperty->getName()])
									) {
										$id = $idProperty->getType()->makeValue(
											$rawValueSet[$idProperty->getName()],
											FetchStrategy::cascade()
										);
									}

									if (isset($id) && $id) {
										try {
											$argument = $dao->getLazyById($id);
											$argument->fetch();
										}
										catch (OrmEntityNotFoundException $e) {
											// fuck
											if (sizeof($rawValueSet) == 1) {
												throw new MvcActionHandleException(
													$context,
													$parameter->getName(),
													$argument,
													'accessing non-existent entity'
												);
											}
										}
									}
									else {
										$argument = $ormClass->getNewEntity();
									}

								}
								else {
									$argument = $ormClass->getNewEntity();
								}

								try {
									$ormClass->getMap()
										->setRawValues(
											$argument,
											$rawValueSet,
											FetchStrategy::cascade()
										);
								}
								catch (Exception $e) {
									throw new MvcActionHandleException(
										$context,
										$parameter->getName(),
										$argument,
										'cannot cast to ' . $class->getName(). ' due to  ' . $e->getMessage()
									);
								}
							}
							else if ($parameter->allowsNull() && !is_null($argument)) {
								throw new MvcActionHandleException(
									$context,
									$parameter->getName(),
									$argument,
									'instance of ' . $class->getName() . ' expected as defined in target action method'
								);
							}
						}
					}
				}
				catch (ArgumentException $e) {
					do {
						if ($this->isOptionalParameter($parameter)) {
							$argument = $parameter->isDefaultValueAvailable()
								? $parameter->getDefaultValue()
								: null;

							break;
						}

						if (($class = $parameter->getClass())) {
							if ($class->getName() == 'Route') {
								$argument = $context->getRouteContext()->getRoute();
								break;
							}

							if ($class->getName() == 'IControllerContext') {
								$argument = $context;
								break;
							}
						}

						throw new ParameterMissingException(
							$context->getRouteContext(),
							$parameter->getName()
						);

					} while (0);
				}
			}

			$arguments[] = $argument;
		}

		return $arguments;
	}

	/**
	 * @return boolean
	 */
	private function isOptionalParameter(ReflectionParameter $parameter)
	{
		return (
			   $parameter->isOptional()
			|| (
				   $parameter->allowsNull()
				&& (
					   $parameter->isArray()
					|| !is_null($parameter->getClass())
				)
			)
		);
	}
}

?>