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
 * @see http://google.com/codesearch?hl=ru&lr=&q=lang%3Ac+package%3Aphp-5.2.1+e_recoverable_error
 * @ingroup Bootstrap
 */
class RecoverableErrorFactory extends StaticClass implements IErrorExceptionFactory
{
	/**
	 * @return ErrorException
	 */
	static function makeException($errstr, $errno, $errfile, $errline)
	{
		Assert::isTrue(
			$errno == E_RECOVERABLE_ERROR,
			'%s supports E_RECOVERABLE_ERROR only',
			__CLASS__
		);

		if (strpos($errstr, 'Argument ') === 0) {
			// argument exception
			return new ArgumentCompatibilityException($errstr, $errno, $errfile, $errline);
		}
		else {
			// operation exception, uncategorized
			return new InternalOperationException($errstr, $errno, $errfile, $errline);
		}
	}
}

?>