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
 * Thrown when the variable is mismatched
 * @ingroup App_Routing_Exceptions
 */
class VariableMatchException extends VariableTypeException
{
	/**
	 * @var array
	 */
	private $predefinedValues = array();

	function __construct(
			$variableName,
			$variableValue,
			IRequestRewriteRule $rule,
			IAppRequest $request,
			array $predefinedValues = array()
		)
	{
		parent::__construct(
			$variableName,
			$variableValue,
			$rule,
			$request,
			'not in set of predefined values'
		);

		$this->predefinedValues = $predefinedValues;
	}

	/**
	 * @return array
	 */
	function getPredefinedValues()
	{
		return $this->predefinedValues;
	}
}

?>