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
	 * @return OrmProperty|null
	 */
	function getIdentifier();

	/**
	 * Gets the set of {@link OrmProperty}
	 * @return array
	 */
	function getProperties();

	/**
	 * @return OrmProperty
	 */
	function getProperty($name);
}

?>