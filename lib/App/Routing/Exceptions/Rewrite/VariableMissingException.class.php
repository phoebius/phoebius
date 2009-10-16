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
 * Thrown when the expected request variable missing
 * @ingroup RewriteRoutingExceptions
 */
class VariableMissingException extends RequestRewriteException
{
	/**
	 * @var string
	 */
	private $variableName;

	/**
	 * @param Route $route
	 * @param string $parameterName
	 */
	function __construct(
			$variableName,
			IRequestRewriteRule $rule,
			IAppRequest $request
		)
	{
		Assert::isScalar($variableName);

		parent::__construct(
			$rule,
			$request,
			'variable not found in request and therefore cannot be rewritten'
		);

		$this->variableName = $variableName;
	}

	/**
	 * @return string
	 */
	function getVariableName()
	{
		return $this->variableName;
	}
}

?>