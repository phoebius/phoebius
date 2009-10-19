<?php
/* ***********************************************************************************************
 *
 * Phoebius Framework
 *
 * **********************************************************************************************
 *
 * Copyright (c) 2009 phoebius.org
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
 * For internal use only. This exception itself represents a wrapper for fatal user defined
 * errors that raised with the little help of {@link trigger_error} and {@link E_USER_ERROR}.
 * Normally, it shouldn't be used to make manual exceptions, use your own custom exceptions that
 * conform your component API
 * @ingroup Core_Exceptions
 * @see Exceptionizer
 */
final class CompilationContextException extends ErrorException implements IErrorExceptionFactory
{
	private $caller;

	/**
	 * @return CompilationContextException
	 */
	static function makeException($errstr, $errno, $errfile, $errline)
	{
		return new self ($errstr, $errno, $errfile, $errline);
	}

	function __construct($errstr, $errno, $errfile, $errline)
	{
		parent::__construct($errstr, 0, $errno, $errfile, $errline);

		$this->resolveCallerHeuristically();
	}

	/**
	 * Gets the actual caller as string representing it's nature
	 * @return string
	 */
	function getCallerAsString()
	{
		$caller = 'unknown';

		if (isset($this->caller['class'])) {
			$caller = $this->caller['class'] . $this->caller['type'] . $this->caller['function'];
		}
		else if (isset($this->caller['function'])) {
			$caller = $this->caller['function'];
		}

		if (isset($this->caller['args'])) {
			$caller .= '(';
			$args = array();
			foreach ($this->caller['args'] as $arg) {
				if (is_scalar($arg)) {
					$args[] = $arg;
				}

				if (is_array($arg)) {
					$args[] = 'Array(sizof=' . sizeof($arg);
				}

				if (is_object($arg)) {
					$args[] = 'object(' . get_class($arg) . ')';
				}
			}

			$caller .= join(',', $args);
			$caller .= ')';
		}

		return $caller;
	}

	private function resolveCallerHeuristically()
	{
		$stackTrace = $this->getTrace();

		$initializerPassed = false;

		foreach ($stackTrace as $track) {
			if (
					   (isset($track['class']) && $track['class'] == 'Assert' && $track['function'] == 'triggerError')
					|| ($track['function'] == 'trigger_error')
			) {
		 		$initializerPassed = true;

		 		continue;
		 	}

		 	if (!$initializerPassed) {
		 		continue;
		 	}

		 	$this->file = isset($track['file']) ? $track['file'] : '';
			$this->line = isset($track['line']) ? $track['line'] : '';
			$this->caller = $track;
			$this->code = $this->getCallerAsString(); //wtf?

			break;
		}
	}
}

?>