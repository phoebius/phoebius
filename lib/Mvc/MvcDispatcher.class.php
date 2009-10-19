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
	 * Current instance of {@link MvcDispatcher}
	 * @var MvcDispatcher
	 */
	static private $current;

	/**
	 * @var array
	 */
	private $controllerFactories = array();

	/**
	 * @return MvcDispatcher
	 */
	static function create()
	{
		return new self;
	}

	/**
	 * @return MvcDispatcher
	 */
	static function getCurrent()
	{
		Assert::isNotEmpty(
			self::$current,
			'any dispatcher is not instantiated yet'
		);

		return self::$current;
	}

	/**
	 * @return void
	 */
	static function setCurrent(MvcDispatcher $dispatcher)
	{
		self::$current = $dispatcher;
	}

	function __construct()
	{
		if (!self::$current) {
			self::$current = $this;
		}
	}

	/**
	 * @return MvcDispatcher an object itself
	 */
	function addControllerFactory(Type $controller, IControllerFactory $controllerFactory)
	{
		Assert::isTrue($controller->isDescendantOf(new Type('Controller')));

		$this->controllerFactories[$controller->getName()] = $controllerFactory;

		return $this;
	}

	/**
	 * @throws RouteHandleException
	 * @return IController
	 */
	function getController(IControllerContext $context)
	{
		try {
			$controllerName = $context->getRouteContext()->getRoute()->getParameter(
				self::PARAMETER_CONTROLLER_NAME,
				$context->getAppContext()->getRequest()
			);
		}
		catch (ArgumentException $e) {
			throw new ParameterMissingException(
				$context->getRouteContext(),
				self::PARAMETER_CONTROLLER_NAME
			);
		}

		$controllerClassName = $this->getControllerClassName($controllerName);

		if (!class_exists($controllerClassName, true)) {
			throw new MvcControllerNotFoundException(
				$context,
				$controllerName,
				$controllerClassName
			);
		}

		if (!Type::create($controllerClassName)->isDescendantOf(new Type('Controller'))) {
			throw new MvcBadControllerException(
				$context,
				$controllerName,
				$controllerClassName
			);
		}

		$controllerObject = $this->getControllerInstance($controllerClassName, $context);

		return $controllerObject;
	}

	/**
	 * @throws RouteHandleException
	 * @return void
	 */
	function handle(IRouteContext $routeContext, IAppContext $appContext)
	{
		$controllerContext = new ControllerContext($routeContext, $appContext);
		$this->getController($controllerContext)->handle($controllerContext);
	}

	/**
	 * @return IController
	 */
	protected function getControllerInstance($controllerClassName, IControllerContext $context)
	{
		if (isset($this->controllerFactories[$controllerClassName])) {
			return call_user_func_array(
				array($this->controllerFactories[$controllerClassName], 'getControllerInstance'),
				array($controllerClassName, $context)
			);
		}
		else {
			return new $controllerClassName;
		}
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