<?php
/* ***********************************************************************************************
 *
 * Phoebius Framework
 *
 * **********************************************************************************************
 *
 * Copyright (c) 2009 Scand Ltd.
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
 * Indicates that type can be produced from scalar
 *
 * @ingroup Core_Types
 */
interface IObjectCastable
{
	/**
	 * Casts scalar to the corresponding object wrapper
	 *
	 * @param scalar
	 * @return IObjectCastable
	 * @throws TypeCastException
	 */
	static function cast($value);

	/**
	 * Gets the scalar value of an object
	 *
	 * @return scalar
	 */
	function getValue();
}

?>