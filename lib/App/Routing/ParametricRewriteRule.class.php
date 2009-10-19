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
 * Two-purpose rewriteRule
 * 1. Add additional custom parameters to the Route
 * 2. Match regular expressions against route parameters.
 *
 * ParametricRewriteRule can be used multiple times with different Routes because when matching
 * the constraints against parameters it does noting if the parameter is not defined within the
 * route (i.e. {@link ParameterMissingException} is not thrown).
 *
 * @ingroup App_Routing
 */
class ParametricRewriteRule implements IRequestRewriteRule
{
	private $parameters = array();
	private $constraints = array();

	/**
	 * @return ParametricRewriteRule
	 */
	static function create()
	{
		return new self;
	}

	/**
	 * @return ParametricRewriteRule an object itself
	 */
	function addParameter($parameterName, $value)
	{
		Assert::isScalar($parameterName);

		$this->parameters[$parameterName] = $value;

		return $this;
	}

	/**
	 * @return ParametricRewriteRule an object itself
	 */
	function addParameters(array $parameters)
	{
		foreach ($parameters as $parameterName => $value) {
			$this->addParameter($parameterName, $value);
		}

		return $this;
	}

	/**
	 * @return ParametricRewriteRule an object itself
	 */
	function setParameters(array $parameters)
	{
		$this->dropParameters();

		foreach ($parameters as $parameterName => $value) {
			$this->addParameter($parameterName, $value);
		}

		return $this;
	}

	/**
	 * @return ParametricRewriteRule an object itself
	 */
	function addConstraint($parameterName, $regexp)
	{
		Assert::isScalar($parameterName);

		$this->constraints[$parameterName] = $regexp;

		return $this;
	}

	/**
	 * @return ParametricRewriteRule an object itself
	 */
	function addConstraints(array $constraints)
	{
		foreach ($constraints as $parameterName => $regexp) {
			$this->addConstraint($parameterName, $regexp);
		}

		return $this;
	}

	/**
	 * @return ParametricRewriteRule an object itself
	 */
	function setConstraints(array $constraints)
	{
		$this->dropConstraints();

		foreach ($constraints as $parameterName => $regexp) {
			$this->addConstraint($parameterName, $regexp);
		}

		return $this;
	}

	/**
	 * @return ParametricRewriteRule an object itself
	 */
	function dropConstraints()
	{
		$this->constraints = array();

		return $this;
	}

	/**
	 * @return ParametricRewriteRule an object itself
	 */
	function dropParameters()
	{
		$this->parameters = array();

		return $this;
	}

	/**
	 * @return boolean
	 */
	function isMatch(IAppRequest $request)
	{
		return true;
	}

	/**
	 * Rewrites the specified request to the resulting route specified
	 * @throws RequestRewriteException
	 * @return Route
	 */
	function rewrite(IAppRequest $request, Route $route)
	{
		foreach ($this->parameters as $parameterName => $value) {
			$route->addParameter($parameterName, $value);
		}

		if (true !== ($parameterName = $this->passConstraints($route, $request))) {
			throw new VariableConstraintException(
				$parameterName,
				$route->getParameter($parameterName, $request),
				$this->constraints[$parameterName],
				$this,
				$request
			);
		}

		return $route;
	}

	/**
	 * Casts the specified route to the request
	 * @throws RouteHandleException
	 * @return IAppRequest
	 */
	function compose(Route $route, IAppRequest $request)
	{
		if (true !== ($parameterName = $this->passConstraints($route, $request))) {
			throw new ParameterConstraintException(
				$route,
				$parameterName,
				$route->getParameter($parameterName, $request),
				$this->constraints[$parameterName]
			);
		}

		// No need to add ParametricRewriteRule::parameters to the request or match them against
		// the constraints, cause those parameters are needed for handling route only

		return $request;
	}

	private function passConstraints(Route $route, IAppRequest $request)
	{
		foreach ($this->constraints as $parameterName => $regexp) {
			try {
				$value = $route->getParameter($parameterName, $request);
				if (!$this->isConstraintMatched($parameterName, $regexp, $value)) {
					return $parameterName;
				}
			}
			catch (ArgumentException $e) {
				// nothing
			}
		}

		return true;
	}

	private function isConstraintMatched($parameterName, $regexp, $value)
	{
		try {
			return preg_match($regexp, $value);
		}
		catch (ExecutionContextException $e) {
			Assert::isFalse(
				true,
				'invalid regexp %s specified for the %s constraint: %s',
				$regexp, $parameterName, $e->getMessage()
			);
		}
	}
}

?>