<?php
/* ***********************************************************************************************
 *
 * Phoebius Framework
 *
 * **********************************************************************************************
 *
 * Copyright (c) 2009 Scand Ltd.
 *
 * This program is free software; you can redistribute it and/or modify it under the terms
 * of the GNU Lesser General Public License as published by the Free Software Foundation;
 * either version 3 of the License, or (at your option) any later version.
 *
 * You should have received a copy of the GNU Lesser General Public License along with
 * this program; if not, see <http://www.gnu.org/licenses/>.
 *
 ************************************************************************************************/

/**
 * ExecutionContextException custom exception is used to wrap PHP errors (occured inside internal
 * functions and methods). Normally, it shouldn't be used to make manual exceptions, use your own
 * custom exceptions that conform your component API
 * @ingroup Core_Exceptions
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