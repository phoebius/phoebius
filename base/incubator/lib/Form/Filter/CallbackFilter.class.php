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
 * Represents the wrapper for callback filter
 *
 */
class CallbackFilter implements IFilter
{
	/**
	 * @var IDelegate
	 */
	private $callback;

	/**
	 * Creates a new wrapper for the callback that is to be used as a filter
	 *
	 * @param IDelegate $callback
	 */
	function __construct(IDelegate $callback)
	{
		$this->callback = $callback;
	}

	/**
	 * Passes the variable throgh the filter and determines whether it passed or failed to pass
	 *
	 * @param mixed $value
	 * @return boolean
	 */
	function filter($value)
	{
		return (boolean)$this->callback->invokeArgs(array($value));
	}

}
?>