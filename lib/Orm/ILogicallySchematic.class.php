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
 * Defines an interface for accessing logical schema information
 * @ingroup Orm
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
	 * @return IOrmProperty|null
	 */
	function getIdentifier();

	/**
	 * Gets the set of propertyName => IOrmProperty
	 * @return array
	 */
	function getProperties();

	/**
	 * @throws ArgumentException
	 * @return IOrmProperty
	 */
	function getProperty($name);
}

?>