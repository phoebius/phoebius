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
 * Thrown when the request variable is of unexpected type
 * @ingroup RewriteRoutingExceptions
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