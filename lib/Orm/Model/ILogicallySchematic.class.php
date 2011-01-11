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
 * Defines an interface for accessing logical schema information
 * @ingroup Orm_Model
 */
interface ILogicallySchematic
{
	/**
	 * Returns the name of the class representing an entity
	 * @return string
	 */
	function getEntityName();

	/**
	 * @return OrmEntity
	 */
	function getNewEntity();

	/**
	 * @return OrmProperty|null
	 */
	function getIdentifier();

	/**
	 * Gets the set of propertyName => OrmProperty
	 * @return array
	 */
	function getProperties();

	/**
	 * @throws ArgumentException
	 * @return OrmProperty
	 */
	function getProperty($name);
	
	/**
	 * Returns the entity property for the specified path
	 * 
	 * @return EntityProperty
	 */
	function getEntityProperty(EntityPropertyPath $path);
}

?>