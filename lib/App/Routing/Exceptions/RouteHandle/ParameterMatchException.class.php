<?php
/* ***********************************************************************************************
 *
 * Phoebius Framework
 *
 * **********************************************************************************************
 *
 * Copyright notice
 *
 ************************************************************************************************/

/**
 * Thrown when the Route parameter is mismatched and therefore the Route cannot be handled
 * @ingroup RouteHandleExceptions
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