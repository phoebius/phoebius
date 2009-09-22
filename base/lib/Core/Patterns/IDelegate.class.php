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
 * Used to implement strict public APIs, where the callable argument should be passed
 * @ingroup Patterns
 */
interface IDelegate
{
	/**
	 * Invokes the delegate
	 * @param mixed $1[,...] the arguments to be passed to the delegate
	 * @return mixed delegate result
	 */
	function invoke();

	/**
	 * Invokes the delegate
	 * @param array $args the arguments to be passed to the delegate
	 * @return mixed delegate result
	 */
	function invokeArgs(array $args = array());
}

?>