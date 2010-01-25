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
 * @see http://google.com/codesearch?hl=ru&lr=&q=lang%3Ac+package%3Aphp-5.2.1+e_recoverable_error
 *
 * @ingroup Core_Bootstrap
 */
class RecoverableErrorFactory extends StaticClass implements IErrorExceptionFactory
{
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