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
 * @ingroup CoreTypes
 */
interface IGenerated
{
	/**
	 * @return mixed
	 */
	function preGenerate(DB $db, $tableName, OrmProperty $ormProperty);

	/**
	 * @return mixed
	 */
	function getGeneratedId(DB $db, $tableName, OrmProperty $ormProperty);
}

?>