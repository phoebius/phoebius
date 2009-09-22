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
 * Represents a so-called static class, that functions like a method container grouped by
 * namespace. By design, static classes cannot have instances. They contain only static
 * helper methods. Refer MSDN to see what static classes are
 * @ingroup Patterns
 */
abstract class StaticClass
{
	/**
	 * Ctor stub to avoid instances of static classes
	 */
	final private function __construct()
	{
		//nothing here
	}
}

?>