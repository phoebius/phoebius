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
 * A basic implementation of IDelegate: represents a callback argument wrapper
 * @ingroup Core_Patterns
 */
final class Delegate implements IDelegate
{
	/**
	 * @var callback
	 */
	private $callback;

	/**
	 * @var $callback callback
	 */
	function __construct($callback)
	{
		Assert::isTrue(
			is_callable($callback),
			'not a callback passed into the wrapper (%s)',
			$this->expandCallback($callback)
		);

		$this->callback = $callback;
	}

	/**
	 * Invokes a passed callback with the arguments passed to method
	 * @param ... mixed arguments to be passed to the callback
	 * @return mixed the result of the invoked callback
	 */
	function invoke()
	{
		$args = func_get_args();
		return call_user_func_array($this->callback, $args);
	}

	/**
	 * Invokes a passed callback with the arguments passed as an array
	 * @param array $args arguments to be passed to the callback
	 * @return mixed the result of the invoked callback
	 */
	function invokeArgs(array $args = array())
	{
		return call_user_func_array($this->callback, $args);
	}

	/**
	 * Expands the callback name to be printed in error log
	 * @return string
	 */
	private function expandCallback($callback)
	{
		if (is_scalar($callback)) {
			return $callback;
		}
		else if (is_array($callback)) {
			if (is_scalar($callback[0])) {
				return $callback[0].'::'.$callback[1];
			}
			else {
				return '('.get_class($callback[0])."){$callback[0]}->{$callback[1]}";
			}
		}
		else {
			return '('.get_class($callback[0])."){$callback[0]}";
		}
	}
}

?>