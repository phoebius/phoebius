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
interface IQueried extends IMapped
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