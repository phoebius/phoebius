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
 * @ingroup MvcExceptions
 */
abstract class MvcHandleException extends ParameterTypeException
{
	/**
	 * @param IControllerContext $context
	 * @param string $parameterName
	 * @param mixed $parameterValue
	 * @param string $message
	 */
	function __construct(IControllerContext $context, $parameterName, $parameterValue, $message = 'unexpected parameter type')
	{
		Assert::isScalar($parameterName);

		parent::__construct($context->getRouteContext(), $parameterName, $parameterValue, $message);

		$this->parameterName = $parameterName;
		$this->parameterValue = $parameterValue;
	}
}

?>