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
 * @ingroup Orm
 */
interface IMappable
{
	/**
	 * @return IOrmEntityMapper
	 */
	function getMap();

	/**
	 * @return ILogicallySchematic
	 */
	function getLogicalSchema();
}

?>