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
 * @ingroup RewriteRoutingExceptions
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