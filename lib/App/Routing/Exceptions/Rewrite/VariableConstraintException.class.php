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
 * @ingroup App_Routing_Exceptions
 */
class VariableConstraintException extends VariableTypeException
{
	private $regexp;

	/**
	 * @param string $parameterName
	 * @param mixed $parameterValue
	 * @param string $message
	 */
	function __construct(
			$variableName,
			$variableValue,
			$regexp,
			IRequestRewriteRule $rule,
			IAppRequest $request
		)
	{
		Assert::isScalar($regexp);

		parent::__construct(
			$variableName,
			$variableValue,
			$rule,
			$request,
			'variable value matched against the regexp constraint failed'
		);

		$this->regexp = $regexp;
	}

	/**
	 * @return string
	 */
	function getRegexp()
	{
		return $this->regexp;
	}
}

?>