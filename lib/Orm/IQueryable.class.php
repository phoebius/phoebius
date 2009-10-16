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
interface IQueryable extends IMappable
{
	/**
	 * @return IOrmEntityAccessor
	 */
	function getDao();

	/**
	 * @return IPhysicallySchematic
	 */
	function getPhysicalSchema();
}

?>