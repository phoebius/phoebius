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
	 * FIXME: introduce setLimit/setOffset
	 * Sets a limit for row selection
	 * @param integer $limit positive integer
	 * @return SelectQuery an object itself
	 */
	function setLimit($limit);
	function setOffset($offset);

}

?>