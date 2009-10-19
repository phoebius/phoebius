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
 * Rewrites:
 *  1. request variables (based on {@link WebRequestPart}
 *  2. Request method (see {@link RequestMethod})
 *  3. request proto (secured, etc)
 * @ingroup App_Routing_Web
 */
class WebRequestRewriteRule implements IRequestRewriteRule
{
	/**
	 * @var array of {@link WebRequestVariable}
	 */
	private $variables = array();

	/**
	 * @var RequestMethod|null
	 */
	private $requestMethod = null;

	/**
	 * @var boolean|null
	 */
	private $secured = null;

	/**
	 * @return WebRequestRewriteRule an object itself
	 */
	function addRequestVariable(
			$variableName,
			WebRequestPart $part = null,
			array $predefinedValues = array(),
			$optional = false,
			$defaultValue = null
		)
	{
		Assert::isScalar($variableName);
		Assert::isFalse(isset($this->variables[$variableName]), 'already defined');

		$this->variables[$variableName] = new WebRequestVariable(
			$part,
			$predefinedValues,
			$optional,
			$defaultValue
		);

		return $this;
	}

	/**
	 * @return HttpRewriteRule an object itself
	 */
	function setSecured()
	{
		$this->secured = true;

		return $this;
	}

	/**
	 * @return HttpRewriteRule an object itself
	 */
	function setNotSecured()
	{
		$this->secured = false;

		return $this;
	}

	/**
	 * @return HttpRewriteRule an object itself
	 */
	function dropSecured()
	{
		$this->secured = null;

		return $this;
	}

	/**
	 * @return boolean
	 */
	function isSecured()
	{
		return $this->secured === true || !is_null($this->secured);
	}

	/**
	 * @return HttpRewriteRule an object itself
	 */
	function setRequestMethod(RequestMethod $method)
	{
		$this->requestMethod = $method;

		return $this;
	}

	/**
	 * @return HttpRewriteRule an object itself
	 */
	function dropRequestMethod()
	{
		$this->requestMethod = null;

		return $this;
	}

	/**
	 * @return RequestMethod|null
	 */
	function getRequestMethod()
	{
		return $this->requestMethod;
	}

	/**
	 * @param IAppRequest $request WebRequest is expected here
	 * @return boolean
	 */
	function isMatch(IAppRequest $request)
	{
		Assert::isTrue(
			$request instanceof WebRequest,
			'WebRequest is only valid request here'
		);

		do {
			if (!is_null($this->secured)) {
				if ($this->isSecured() !== $request->isSecured()) {
					break;
				}
			}

			if ($this->requestMethod) {
				if (!$request->getRequestMethod()->isEqual($this->requestMethod)) {
					break;
				}
			}

			foreach ($this->variables as $name => $requestVariable) {
				Assert::isTrue($requestVariable instanceof WebRequestVariable);

				$value = null;

				try {
					$value = $request->getVariable(
						$name,
						$requestVariable->getWebRequestPart()
					);
				}
				catch (ArgumentException $e) {
					if ($requestVariable->isOptional()) {
						if ($requestVariable->hasDefaultValue()) {
							$value = $requestVariable->getDefaultValue();
						}
						else {
							continue;
						}
					}
					else {
						break 2;
					}
				}

				if ($requestVariable->getPredefinedValuesCount() > 0) {
					if (!in_array($value, $requestVariable->getPredefinedValues())) {
						break 2;
					}
				}
			}

			return true;

		} while (0);

		return false;
	}

	/**
	 * @throws RequestRewriteException
	 * @return Route
	 */
	function rewrite(IAppRequest $request, Route $route)
	{
		Assert::isTrue(
			$request instanceof WebRequest,
			'WebRequest is only valid request here'
		);

		foreach ($this->variables as $name => $requestVariable) {
			Assert::isTrue($requestVariable instanceof WebRequestVariable);

			$value = null;

			try {
				$value = $request->getVariable(
					$name,
					$requestVariable->getWebRequestPart()
				);
			}
			catch (ArgumentException $e) {
				if ($requestVariable->isOptional()) {
					if ($requestVariable->hasDefaultValue()) {
						$value = $requestVariable->getDefaultValue();
					}
					else {
						continue;
					}
				}
				else {
					throw new VariableMissingException(
						$name, $this, $request
					);
				}
			}

			if ($requestVariable->getPredefinedValuesCount() > 0) {
				if (!in_array($value, $requestVariable->getPredefinedValues())) {
					throw new VariableMatchException(
						$name,
						$value,
						$this,
						$request,
						$requestVariable->getPredefinedValues()
					);
				}
			}

			$route->addParameter($name, $value);
		}

		return $route;
	}

	/**
	 * FIXME: Parameter*Exception ctor accepts IRouteContext, but Route is only available from this context
	 * @throws RouteHandleException
	 * @return Request
	 */
	function compose(Route $route, IAppRequest $request)
	{
		Assert::isTrue(
			$request instanceof WebRequest,
			'WebRequest rewriter only'
		);

		if ($this->requestMethod) {
			$request->setRequestMethod($this->requestMethod);
		}

		if (!is_null($this->secured)) {
			$this->isSecured()
				? $request->setSecured()
				: $request->setNotSecured();
		}

		$parameters = $route->getParameters();

		foreach ($this->variables as $name => $requestVariable) {
			Assert::isTrue($requestVariable instanceof WebRequestVariable);

			$value = null;

			if (isset($parameters[$name])) {
				$value = $parameters[$name];

				if ($requestVariable->getPredefinedValuesCount()) {
					if (!in_array($value, $requestVariable->getPredefinedValues())) {
						throw new ParameterMatchException(
							$route,
							$name,
							$value,
							$requestVariable->getPredefinedValues()
						);
					}
				}
			}
			else {
				if ($requestVariable->isOptional()) {
					if ($requestVariable->hasDefaultValue()) {
						$value = $requestVariable->getDefaultValue();
					}
					else {
						continue;
					}
				}
				else if ($requestVariable->getPredefinedValuesCount() == 1) {
					$value = reset($requestVariable->getPredefinedValues());
				}
				else {
					throw new ParameterMissingException($route, $name);
				}
			}

			$request->setVariable(
				$name,
				$requestVariable->getWebRequestPart(),
				$value
			);
		}

		return $request;
	}
}

?>