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
 * Thrown when the Route parameter is mismatched and therefore the Route cannot be handled
 * @ingroup App_Routing_Exceptions
 */
class ParameterMatchException extends ParameterTypeException
{
	/**
	 * @var array
	 */
	private $predefinedValues = array();

	function __construct(
			IRouteContext $context,
			$parameterName,
			$parameterValue,
			array $predefinedValues = array()
		)
	{
		parent::__construct(
			$context,
			$parameterName,
			$parameterValue,
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