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
 * Defines an interface for accessing physical schema information that stores a internal
 * data
 * @ingroup Orm
 */
interface IPhysicallySchematic
{
	/**
	 * Gets the name of the DB table where entities are stored
	 * @return string
	 */
	function getDBTableName();

	/**
	 * Array of columnName => DBType
	 * @return array
	 */
	function getDbColumns(OrmProperty $property = null);

	/**
	 * @return IOrmQuery
	 */
	function getOrmQuery();
}

?>