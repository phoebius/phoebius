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
 * Represents a classic singleton class API
 * @ingroup Patterns
 */
interface ISingleton
{
	/**
	 * Returns the instance of the singleton class
	 * @return ISingleton
	 */
	static function getInstance();
}

?>