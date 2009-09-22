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
interface IMapped
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