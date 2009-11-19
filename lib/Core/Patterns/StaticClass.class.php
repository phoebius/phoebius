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
 * Represents a so-called static class, that functions like a method container.
 *
 * By design, static classes cannot have instances. They contain only static
 * helper methods.
 *
 * @ingroup Core_Patterns
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