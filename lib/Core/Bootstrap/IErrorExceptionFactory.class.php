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
 * @ingroup Bootstrap
 */
interface IErrorExceptionFactory
{
	/**
	 * @return ErrorException
	 */
	static function makeException($errstr, $errno, $errfile, $errline);
}

?>