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
 * @ingroup App
 */
interface IAppRequest
{
	/**
	 * @throws ArgumentException
	 */
	function getAnyVariable($variable);

	/**
	 * @return IAppRequest
	 */
	function getCleanCopy();
}

?>