<?php
/* ***********************************************************************************************
 *
 * Phoebius Framework
 *
 * **********************************************************************************************
 *
 * Copyright (c) 2010 Scand Ltd.
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
 * Contract for a type that exposes a property type handler for itself
 *
 * @ingroup Orm_Types
 */
interface IOrmPropertyAssignable
{
	/**
	 * Gets the OrmPropertyType property type handler that is responsible
	 * for type representation and mapping
	 *
	 * @return OrmPropertyType
	 */
	function getOrmPropertyType();
}

?>