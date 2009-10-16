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
 * @ingroup RouteHandleExceptions
 */
class ParameterConstraintException extends ParameterTypeException
{
	private $regexp;

	/**
	 * @param string $parameterName
	 * @param mixed $parameterValue
	 * @param string $message
	 */
	function __construct(
			IRouteContext $context,
			$parameterName,
			$parameterValue,
			$regexp
		)
	{
		Assert::isScalar($regexp);

		parent::__construct(
			$context,
			$parameterName,
			$parameterValue,
			'parameter value matched against the regexp constraint failed'
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