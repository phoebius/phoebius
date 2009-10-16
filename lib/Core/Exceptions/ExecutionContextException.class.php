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
 * ExecutionContextException custom exception is used to wrap PHP errors (occured inside internal
 * functions and methods). Normally, it shouldn't be used to make manual exceptions, use your own
 * custom exceptions that conform your component API
 * @ingroup CoreExceptions
 * @see Exceptionizer
 */
class ExecutionContextException extends ErrorException implements IErrorExceptionFactory
{
	/**
	 * @return ExecutionContextException
	 */
	static function makeException($errstr, $errno, $errfile, $errline)
	{
		return new self ($errstr, $errno, $errfile, $errline);
	}

	function __construct($errstr, $errno, $errfile, $errline)
	{
		parent::__construct($errstr, 0, $errno, $errfile, $errline);
	}
}

?>