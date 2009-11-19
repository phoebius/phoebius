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
 * Exceptionizer is the signleton that translates the specified errors occured at runtime into
 * exceptions. Phoebius Framework promotes using exceptions instead of errors to make code more
 * predictible.
 * @ingroup Core_Bootstrap
 */
final class Exceptionizer extends LazySingleton
{
	/**
	 * @var int
	 */
	private $mask = 0;

	/**
	 * @var boolean
	 */
	private $supressUncovered = false;

	/**
	 * @var boolean
	 */
	private $registered = false;

	/**
	 * @var callback|null
	 */
	private $previousErrorHandler = null;

	private $translatableErrorTypes = array(
		E_WARNING, E_NOTICE, E_STRICT,
		E_USER_ERROR, E_USER_WARNING, E_USER_NOTICE,
		E_RECOVERABLE_ERROR, E_ALL
	);

	private $defaultException;
	private $exceptionsTable = array();

	/**
	 * Gets the instance of singleton class
	 * @return Exceptionizer an object itself
	 */
	static function getInstance()
	{
		if (!LazySingleton::isInstantiated(__CLASS__)) {
			sort(LazySingleton::instance(__CLASS__)->translatableErrorTypes);
		}

		return LazySingleton::instance(__CLASS__);
	}

	/**
	 * Registers an exceptionizer with the specified parameters that could be changed at runtime
	 * @param integer $coverMask integer mask to specify what errors should be translated to
	 * 	exceptions. E.g., if you specify E_WARNING | E_NOTICE as the cover mask, E_NOTICE
	 *  and E_WARNING errors would be translated to exceptions, but E_ERROR is not.
	 * 	Default is E_ALL.
	 * @param boolean $supressUncovered specifies what to do with errors that do not match the
	 * 	cover mask. If you wish to supress them and let the default handler to process those errors
	 * 	than you can set this argument to false.
	 * @param string $defaultExceptionName name of the exception to be thrown when translatable
	 * 	error occurs. Default is Exception
	 * @return Exceptionizer an object itself
	 */
	function register($coverMask = E_ALL, $supressUncovered, $defaultExceptionName)
	{
		$this->setCoverMask($coverMask);
		$this->setUncoveredErrorsIgnorance($supressUncovered);
		$this->setDefaultException($defaultExceptionName);


		if (!$this->registered) {
			$this->previousErrorHandler = set_error_handler(array($this, 'errorHandler'));
		}

		$this->registered = true;

		return $this;
	}

	/**
	 * Sets the default exception name to which the errors should be casted
	 * @param string $exceptionName name of the exception class that implements {@link IErrorExceptionFactory}
	 * @return Exceptionizer an object itself
	 */
	function setDefaultException($exceptionName)
	{
		Assert::isTrue(
			TypeUtils::isChild($exceptionName, 'IErrorExceptionFactory')
		);

		$this->defaultException = $exceptionName;

		return $this;
	}

	/**
	 * Sets the exception to a separate error type to be casted to. I.e. you can cast all errors
	 * to the {@link ExecutionContextException} and E_USER_ERROR to {@link CompilationContextException}
	 * @param integer $errorTypean separate error type (not a mask!)
	 * @param string $exceptionName name of an exception class that implements {@link IErrorExceptionFactory}
	 * @return Exceptionizer an object itself
	 */
	function setException($errorType, $exceptionName)
	{
		Assert::isTrue(in_array($errorType, $this->translatableErrorTypes));
		Assert::isTrue(
			TypeUtils::isChild($exceptionName, 'IErrorExceptionFactory')
		);

		if ($errorType == E_ALL) {
			$this->setDefaultException($exceptionName);
		}
		else {
			$this->exceptionsTable[$errorType] = $exceptionName;
		}

		return $this;
	}

	/**
	 * Drops an exception set to a custom error type so that this error type will now be casted
	 * to a default exception class
	 * @param integer $errorTypean separate error type (not a mask!)
	 * @return Exceptionizer an object itself
	 */
	function dropErrorTypedException($errorType)
	{
		Assert::isTrue(in_array($errorType, $this->translatableErrorTypes));

		unset($this->exceptionsTable[$errorType]);

		return $this;
	}

	/**
	 * Drops all exceptinons set to the custom error types so that now all error types covered
	 * by a mask will be casted to a default exception class
	 * @return Exceptionizer an object itself
	 */
	function dropErrorTypedExceptions()
	{
		$this->exceptionsTable = array();

		return $this;
	}

	/**
	 * Sets the mask to what errors should be translated to exceptions. E.g., if you specify
	 * E_WARNING | E_NOTICE as the cover mask, E_NOTICE and E_WARNING errors would be translated
	 * to exceptions, but E_ERROR is not.
	 * @param integer $coverMask integer mask to specify what errors should be translated to
	 * 	exceptions. E.g., if you specify E_WARNING | E_NOTICE as the cover mask, E_NOTICE
	 *  and E_WARNING errors would be translated to exceptions, but E_ERROR is not.
	 * 	Default is E_ALL.
	 * @return Exceptionizer an object itself
	 */
	function setCoverMask($coverMask = E_ALL)
	{
		Assert::isNumeric($coverMask);
		Assert::isTrue(!!($coverMask & E_ALL), 'not an error mask');

		$this->mask = $coverMask;

		return $this;
	}

	/**
	 * Specifies what to do with errors that do not match the cover mask. If you wish to supress
	 * them and let the default handler to process those errors than you can set this argument to
	 * false.
	 * @param boolean $supressUncovered specifies what to do with errors that do not match the
	 * 	cover mask. If you wish to supress them and let the default handler to process those errors
	 * 	than you can set this argument to false.
	 * @return Exceptionizer an object itself
	 */
	function setUncoveredErrorsIgnorance($flag = false)
	{
		Assert::isBoolean($flag);

		$this->supressUncovered = $flag;

		return $this;
	}

	/**
	 * Specifies that errors that do not match the cover mask specified, should be supressed
	 * @return Exceptionizer an object itself
	 */
	function supressUncovered()
	{
		$this->setUncoveredErrorsIgnorance(true);

		return $this;
	}

	/**
	 * Specifies that errors that match the cover mask specified, should be handled by the default
	 * error handler
	 * @return Exceptionizer an object itself
	 */
	function handleUncovered()
	{
		$this->setUncoveredErrorsIgnorance(false);

		return $this;
	}

	/**
	 * Unregisters an exceptionizer
	 * @return Exceptionizer
	 */
	function unregister()
	{
		Assert::isTrue($this->registered, 'not yet registered as autoloader');

		restore_error_handler();

		return $this;
	}

	/**
	 * Default error handler, that casts covered errors to their appropriate exceptions
	 * @return boolean
	 */
	function errorHandler($errno, $errstr, $errfile, $errline)
	{
		if (!error_reporting()) {
			//return false;
		}

		if (!( $errno & $this->mask)) {
			if (!$this->supressUncovered) {
				if ($this->previousErrorHandler) {
					$args = func_get_args();
					call_user_func_array($this->prevErrorHandler, $args);
				}
				else {
					return false;
				}
			}

			return true;
		}

		if (isset ($this->exceptionsTable[$errno])) {
			$exceptionClass = $this->exceptionsTable[$errno];

			Assert::isTrue(
				TypeUtils::isChild($exceptionClass, 'IErrorExceptionFactory')
			);

			$exceptionObject = call_user_func_array(
				array($exceptionClass, 'makeException'),
				array($errstr, $errno, $errfile, $errline)
			);
		}
		else {
			$exceptionObject = null;
		}

		if (!($exceptionObject instanceof Exception)) {
			$exceptionClass = $this->defaultException;

			Assert::isTrue(
				TypeUtils::isChild($exceptionClass, 'IErrorExceptionFactory')
			);

			$exceptionObject = call_user_func_array(
				array($exceptionClass, 'makeException'),
				array($errstr, $errno, $errfile, $errline)
			);
		}

		throw $exceptionObject;
	}
}

?>