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
 * Thrown when the Route parameter is of bad type and therefore the Route cannot be handled
 * @ingroup App_Routing_Exceptions
 */
class ParameterTypeException extends RouteHandleException
{
	/**
	 * @var string
	 */
	private $parameterName;

	/**
	 * @var mixed
	 */
	private $parameterValue;

	function __construct(IRouteContext $context, $parameterName, $parameterValue, $message = 'unexpected parameter type')
	{
		Assert::isScalar($parameterName);

		parent::__construct($context, $message);

		$this->parameterName = $parameterName;
		$this->parameterValue = $parameterValue;
	}

	/**
	 * @return string
	 */
	function getParameterName()
	{
		return $this->parameterName;
	}

	/**
	 * @return mixed
	 */
	function getParameterValue()
	{
		return $this->parameterValue;
	}
}

?>