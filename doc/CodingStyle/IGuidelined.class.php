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
 * API to make a class (an implementation of the interface) follow the guideline.
 * @ingroup CodingStyle
 */
interface IGuidelined
{
	/**
	 * Validates the class code to make it follow the guideline
	 * @throws ConventionException
	 * @return void
	 */
	function validateAgainst(IConvention $convention);
}

?>