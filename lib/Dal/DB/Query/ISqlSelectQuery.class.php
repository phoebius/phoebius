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
 * Represents an object that can be casted to a plain string SQL query for selecting data
 * @ingroup Query
 */
interface ISqlSelectQuery extends ISqlQuery
{
	/**
	 * Sets a limit for row selection
	 * @param integer $limit positive integer
	 * @return ISqlSelectQuery an object itself
	 */
	function setLimit($limit);

	/**
	 * Sets an offset for row selection
	 * @param integer $offset positive integer
	 * @return ISqlSelectQuery an object itself
	 */
	function setOffset($offset);

}

?>