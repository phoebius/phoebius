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
 * Defines an interface for accessing physical schema information
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
	 * Array of field names
	 * @return array
	 */
	function getDBFields();
}

?>