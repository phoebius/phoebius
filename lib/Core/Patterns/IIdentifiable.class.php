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
 * Represents an object, identifiered by an identifier, that could be retrieved or set manually
 * @ingroup Core_Patterns
 */
interface IIdentifiable
{
	/**
	 * Gets the identifier of an object
	 * @return scalar
	 */
	function getId();

	/**
	 * Sets the identifier of an object
	 * @param scalar $id
	 * @return IIdentifiable an object itself
	 */
	function setId($id);
}

?>