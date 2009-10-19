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
 * Thrown when the Route parameter is missing and therefore the Route cannot be handled
 * @ingroup App_Routing_Exceptions
 */
class ParameterMissingException extends RouteHandleException
{
	/**
	 * @var string
	 */
	private $parameterName;

	function __construct(IRouteContext $context, $parameterName)
	{
		Assert::isScalar($parameterName);

		parent::__construct($context);

		$this->parameterName = $parameterName;
	}

	/**
	 * @return string
	 */
	function getParameterName()
	{
		return $this->parameterName;
	}
}

?>