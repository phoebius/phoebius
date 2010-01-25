<?php
/* ***********************************************************************************************
 *
 * Phoebius Framework
 *
 * **********************************************************************************************
 *
 * Copyright (c) 2009 Scand Ltd.
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
 * Defines an object that represents a database query for selecting tuples
 * @ingroup Dal_DB_Query
 */
interface ISqlSelectQuery extends ISqlQuery
{
	/**
	 * Sets the maximum number of rows to return
	 * @param integer $limit positive integer
	 * @return ISqlSelectQuery itself
	 */
	function setLimit($limit);

	/**
	 * Sets the number of rows to skip before starting to return rows
	 * @param integer $offset positive integer
	 * @return ISqlSelectQuery itself
	 */
	function setOffset($offset);

}

?>