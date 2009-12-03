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
 * Defines an entity that is related to ORM. Such an entity can be assembled and disassembled by
 * IOrmEntityMapper
 *
 * @ingroup Orm
 */
interface IOrmRelated
{
	/**
	 * Gets the entity mapper
	 *
	 * @return IOrmEntityMapper
	 */
	static function map();

	/**
	 * Gets the entity auxiliary container
	 *
	 * @return IMappable
	 */
	static function orm();
}

?>