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
 * Thrown when the Route parameter is of bad type and therefore the Route cannot be handled
 * @ingroup RouteHandleExceptions
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