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
 * Thrown when the variable is mismatched
 * @ingroup RewriteRoutingExceptions
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