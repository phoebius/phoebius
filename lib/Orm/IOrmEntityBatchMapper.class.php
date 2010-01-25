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
 * Contract for optimized ORM-related entity mapper.
 *
 * This mapper should fill the entities at once by collecting the primitive values and then
 * mapping it to property objects at once, if possible.
 *
 * @ingroup Orm
 */
interface IOrmEntityBatchMapper extends IOrmEntityMapper
{
	/**
	 * Tells the mapper that all entities are queued and now can be filled
	 *
	 * @return void
	 */
	function finish();
}

?>