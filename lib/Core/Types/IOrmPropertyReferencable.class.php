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
 * Represents a type that can be assigned to OrmEntity property as identifier and foreign key.
 *
 * @ingroup Core_Types
 */
interface IOrmPropertyReferencable
{
	/**
	 * Gets the auxiliary type wrapper that describes the foreign key
	 *
	 * @return OrmPropertyType
	 */
	function getReferenceType(AssociationMultiplicity $multiplicity);
}

?>