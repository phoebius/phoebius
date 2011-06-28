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
 * Helper class containing a set of methods for making easy-to-read and easy-to-use assertions
 * @ingroup Core
 */
final class Assert extends StaticClass
{
	/**
	 * Checks if assertion is false
	 * @warning Weak-typing comparison (==) is used in assertion check
	 * @param mixed $assertion
	 * @param string $message optional string to be printed when assertion check fails
	 * @return void
	 */
	static function isFalse($assertion, $message = null)
	{
		if (false != $assertion) {
			$args = func_get_args();
			self::triggerError(array_slice($args, 1));
		}
	}

	/**
	 * Checks if assertion is true
	 * @warning Weak-typing comparison (==) is used in assertion check
	 * @param mixed $assertion
	 * @param string $message optional string to be printed when assertion check fails
	 * @return void
	 */
	static function isTrue($assertion, $message = null)
	{
		if (!$assertion) {
			$args = func_get_args();
			self::triggerError(array_slice($args, 1));
		}
	}

	/**
	 * Checks if assertion is callback
	 * @param mixed $assertion
	 * @param string $message optional string to be printed when assertion check fails
	 * @return void
	 */
	static function isCallback($assertion, $message = 'callable type should be passed')
	{
		if (!is_callable($assertion)) {
			$args = func_get_args();
			self::triggerError(array_slice($args, 1));
		}
	}

	/**
	 * Checks if the specified key is defined inside an array
	 * @return void
	 */
	static function hasIndex(array $list, $key, $message = null)
	{
		if (!array_key_exists($key, $list)) {
			if (!$message) {
				$args = array(
					'%s is not defined inside an array %s',
					$key,
					$list
				);
			}
			else {
				$args = func_get_args();
				$args = array_slice($args, 2);
			}

			self::triggerError($args);
		}
	}

	/**
	 * Checks if assertion is empty
	 * @param mixed $assertion
	 * @param string $message optional string to be printed when assertion check fails
	 * @return void
	 */
	static function isEmpty($assertion, $message = null)
	{
		if (!empty ($assertion)) {
			if (!$message) {
				$args = array(
					'argument is expected to be empty, [%s] given',
					$assertion
				);
			}
			else {
				$args = func_get_args();
				$args = array_slice($args, 1);
			}

			self::triggerError($args);
		}
	}

	/**
	 * Checks if assertion is not empty
	 * @param mixed $assertion
	 * @param string $message optional string to be printed when assertion check fails
	 * @return void
	 */
	static function isNotEmpty($assertion, $message = null)
	{
		if (empty ($assertion)) {
			if (!$message) {
				$args = array(
					'argument is expected to be NOT empty, [%s] given',
					$assertion
				);
			}
			else {
				$args = func_get_args();
				$args = array_slice($args, 1);
			}

			self::triggerError($args);
		}
	}

	/**
	 * Checks if assertion is null
	 * @param mixed $assertion
	 * @param string $message optional string to be printed when assertion check fails
	 * @return void
	 */
	static function isNull($assertion, $message = null)
	{
		if (!is_null($assertion)) {
			if (!$message) {
				$args = array(
					'argument is expected to be null, [%s] given',
					$assertion
				);
			}
			else {
				$args = func_get_args();
				$args = array_slice($args, 1);
			}

			self::triggerError($args);
		}
	}

	/**
	 * Checks if assertion is not null
	 * @param mixed $assertion
	 * @param string $message optional string to be printed when assertion check fails
	 * @return void
	 */
	static function isNotNull($assertion, $message = null)
	{
		if (is_null($assertion)) {
			if (!$message) {
				$args = array(
					'argument is expected to be NOT null, [%s] given',
					$assertion
				);
			}
			else {
				$args = func_get_args();
				$args = array_slice($args, 1);
			}

			self::triggerError($args);
		}
	}

	/**
	 * Checks if assertion is a valid resource
	 * @param mixed $assertion
	 * @param string $message optional string to be printed when assertion check fails
	 * @return void
	 */
	static function isResource($variable, $message = null)
	{
		if (!is_resource($variable)) {
			if (!$message) {
				$args = array(
					'variable is expected to be resource, [%s] given',
					$variable
				);
			}
			else {
				$args = func_get_args();
				$args = array_slice($args, 1);
			}

			self::triggerError($args);
		}
	}

	/**
	 * Checks if assertion is numeric
	 * @param mixed $assertion
	 * @param string $message optional string to be printed when assertion check fails
	 * @return void
	 */
	static function isNumeric($variable, $message = null)
	{
		if (!is_numeric($variable)) {
			if (!$message) {
				$args = array(
					'variable is expected to be numeric, [%s] given',
					$variable
				);
			}
			else {
				$args = func_get_args();
				$args = array_slice($args, 1);
			}

			self::triggerError($args);
		}
	}

	/**
	 * Checks if assertion is integer
	 * @notice Internal is_integer function is not used, special checks are provided by TypeUtils
	 * @param mixed $assertion
	 * @param string $message optional string to be printed when assertion check fails
	 * @return void
	 */
	static function isInteger($variable, $message = null)
	{
		if (!TypeUtils::isInteger($variable)) {
			if (!$message) {
				$args = array(
					'variable is expected to be integer, [%s] given',
					$variable
				);
			}
			else {
				$args = func_get_args();
				$args = array_slice($args, 1);
			}

			self::triggerError($args);
		}
	}

	/**
	 * Checks if assertion is positive integer
	 * @notice Internal is_integer function is not used, special checks are provided by TypeUtils
	 * @param mixed $assertion
	 * @param string $message optional string to be printed when assertion check fails
	 * @return void
	 */
	static function isPositiveInteger($variable, $message = null)
	{
		if (
				   !TypeUtils::isInteger($variable)
				|| $variable < 0
		) {
			if (!$message) {
				$args = array(
					'variable is expected to be positivoe integer, [%s] given',
					$variable
				);
			}
			else {
				$args = func_get_args();
				$args = array_slice($args, 1);
			}

			self::triggerError($args);
		}
	}

	/**
	 * Checks if assertion is float
	 * @param mixed $assertion
	 * @param string $message optional string to be printed when assertion check fails
	 * @return void
	 */
	static function isFloat($variable, $message = null)
	{
		if (
				!(
					   is_numeric($variable)
					&& ($variable == (float) $variable)
				)
		) {
			if (!$message) {
				$args = array(
					'variable is expected to be float, [%s] given',
					$variable
				);
			}
			else {
				$args = func_get_args();
				$args = array_slice($args, 1);
			}

			self::triggerError($args);
		}
	}

	/**
	 * Checks if assertion is scalar
	 * @param mixed $assertion
	 * @param string $message optional string to be printed when assertion check fails
	 * @return void
	 */
	static function isScalar($variable, $message = null)
	{
		if (!is_scalar($variable)) {
			if (!$message) {
				$args = array(
					'variable is expected to be scalar, [%s] given',
					$variable
				);
			}
			else {
				$args = func_get_args();
				$args = array_slice($args, 1);
			}

			self::triggerError($args);
		}
	}

	/**
	 * Checks if assertion is scalar. Alias for Assert::isScalar().
	 * @param mixed $assertion
	 * @param string $message optional string to be printed when assertion check fails
	 * @return void
	 */
	static function isString($variable, $message = null)
	{
		if (!is_scalar($variable)) {
			if (!$message) {
				$args = array(
					'variable is expected to be scalar, [%s] given',
					$variable
				);
			}
			else {
				$args = func_get_args();
				$args = array_slice($args, 1);
			}

			self::triggerError($args);
		}
	}

	/**
	 * Checks if object is instance of type
	 * @return void
	 */
	static function isInstance($object, $type, $message = null)
	{
		if (!($object instanceof $type)) {
			if (!$message) {
				$args = array(
					'object is expected to be instance of %s, %s given',
					$type, TypeUtils::getName($object)
				);
			}
			else {
				$args = func_get_args();
				$args = array_slice($args, 1);
			}

			self::triggerError($args);
		}
	}

	/**
	 * Checks if assertion is scalar
	 * @param mixed $assertion
	 * @param string $message optional string to be printed when assertion check fails
	 * @return void
	 */
	static function isScalarOrNull($variable, $message = null)
	{
		if (!is_null($variable) && !is_scalar($variable)) {
			if (!$message) {
				$args = array(
					'variable is expected to be scalar or null, [%s] given',
					$variable
				);
			}
			else {
				$args = func_get_args();
				$args = array_slice($args, 1);
			}

			self::triggerError($args);
		}
	}

	/**
	 * Checks if assertion is boolean
	 * @param mixed
	 * @param string $message optional string to be printed when assertion check fails
	 * @return void
	 */
	static function isBoolean($variable, $message = null)
	{
		if (
				!(
					   $variable === true
					|| $variable === false
				)
		) {
			if (!$message) {
				$args = array(
					'variable is expected to be boolean, [%s] given',
					$variable
				);
			}
			else {
				$args = func_get_args();
				$args = array_slice($args, 1);
			}

			self::triggerError($args);
		}
	}

	/**
	 * Halts the execution when triggered. Used to assert the execution of unreachable
	 * portions of code
	 * @param string $message optional string to be printed
	 * @return void
	 */
	static function isUnreachable($message = 'unreachable code reached')
	{
		$args = func_get_args();
		self::triggerError($args);
	}


	/**
	 * Halts the execution when triggered. Used to assert the execution of unimplemented
	 * portions of code
	 * @param string $message optional string to be printed
	 * @return void
	 */
	static function notImplemented($message = 'this portion of code is not implemented')
	{
		$args = func_get_args();
		self::triggerError($args);
	}

	/**
	 * Halts the execuition with the message. trigger_error and E_USER_ERROR is used
	 */
	private static function triggerError(array $args)
	{
		if (empty($args) || empty($args[0])) {
			$args[0] = 'assertion failed';
		}
		
		$string = call_user_func_array(array ('DebugUtils', 'sprintf'), $args);
		throw new Exception($string);
	}
}

?>