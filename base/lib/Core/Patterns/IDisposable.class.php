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
 * Honour the tradition of disposable resources, as in .NET
 * @ingroup Patters
 */
interface IDisposable
{
	/**
	 * Closes the resources held by an object
	 * @return IDisposable an object itself
	 */
	function dispose();
}

?>