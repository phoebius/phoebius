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
 * ArgumentException is thrown when a method is invoked and at least one of the passed arguments
 * does not meet the parameter specification of the called method. All instances of
 * ArgumentException should carry a meaningful error message describing the invalid argument,
 * as well as the expected range of values for the argument.
 * @ingroup CoreExceptions
 */
class ArgumentException extends ApplicationException
{
	protected $argumentName;

	/**
	 * @param string $argumentName
	 * @param string $message
	 */
	function __construct($argumentName, $message)
	{
		Assert::isScalar($argumentName);

		parent::__construct($message);

		$this->argumentName = $argumentName;
	}

	/**
	 * Returns the argument name that caused an exception
	 * @return string
	 */
	function getArgumentName()
	{
		return $this->argumentName;
	}
}

?>