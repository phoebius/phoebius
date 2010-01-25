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
 * Defines an entity that is related to ORM and is stored in the database.
 * Such an entity can be assembled and disassembled by IOrmEntityMapper and queried by IOrmEntityAccessor
 *
 * @ingroup Orm_Dao
 */
interface IDaoRelated extends IOrmRelated
{
	/**
	 * Gets the object that allows querying
	 *
	 * @return IOrmEntityAccessor
	 */
	static function dao();

	/**
	 * Gets the high-level query
	 *
	 * @return EntityQuery
	 */
	static function query();
}

?>