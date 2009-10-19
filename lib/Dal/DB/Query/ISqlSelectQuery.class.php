<?php
/* ***********************************************************************************************
 *
 * Phoebius Framework
 *
 * **********************************************************************************************
 *
 * Copyright (c) 2009 phoebius.org
 *
 * This program is free software; you can redistribute it and/or modify it under the terms
 * of the GNU Lesser General Public License as published by the Free Software Foundation;
 * either version 3 of the License, or (at your option) any later version.
 *
 * You should have received a copy of the GNU Lesser General Public License along with
 * this program; if not, see <http://www.gnu.org/licenses/>.
 *
 ************************************************************************************************/

/**
 * Represents an object that can be casted to a plain string SQL query for selecting data
 * @ingroup Dal_DB_Query
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