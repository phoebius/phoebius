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
 * @ingroup CoreExceptions
 */
class ArgumentTypeException extends ArgumentException
{
	/**
	 * @var string
	 */
	private $expectedType;

	/**
	 * @param string $argumentName
	 * @param string $message
	 */
	function __construct($argumentName, $expectedType, $message = 'unexpected argument type')
	{
		Assert::isScalar($expectedType);

		parent::__construct($argumentName, $expectedType, $message);

		$this->expectedType = $expectedType;
	}
}

?>