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
 * Used to implement strict public APIs, where the callable argument should be passed
 * @ingroup Core_Patterns
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