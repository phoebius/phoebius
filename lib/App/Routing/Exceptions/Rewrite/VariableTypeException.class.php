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
 * Thrown when the request variable is of unexpected type
 * @ingroup App_Routing_Exceptions
 */
class VariableTypeException extends RequestRewriteException
{
	/**
	 * @var string
	 */
	private $variableName;

	/**
	 * @var mixed
	 */
	private $variableValue;

	/**
	 * @param string $parameterName
	 * @param mixed $parameterValue
	 * @param string $message
	 */
	function __construct(
			$variableName,
			$variableValue,
			IRequestRewriteRule $rule,
			IAppRequest $request,
			$message
		)
	{
		Assert::isScalar($variableName);

		parent::__construct($rule, $request, $message);

		$this->variableName = $variableName;
		$this->variableValue = $variableValue;
	}

	/**
	 * @return string
	 */
	function getVariableName()
	{
		return $this->variableName;
	}

	/**
	 * @return mixed
	 */
	function getVariableValue()
	{
		return $this->variableValue;
	}
}

?>