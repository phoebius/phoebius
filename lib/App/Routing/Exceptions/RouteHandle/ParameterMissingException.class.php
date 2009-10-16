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
 * Thrown when the Route parameter is missing and therefore the Route cannot be handled
 * @ingroup RouteHandleExceptions
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