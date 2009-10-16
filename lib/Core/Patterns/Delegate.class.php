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
 * A basic implementation of IDelegate: represents a callback argument wrapper
 * @ingroup Patterns
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