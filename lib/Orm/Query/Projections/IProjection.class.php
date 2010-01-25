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
 * Contract for query projections needed for querying ORM-related objects
 *
 * @see Projection as a shorthand for mostly-used projections
 *
 * @ingroup Orm_Query_Projections
 */
interface IProjection
{
	/**
	 * Fills the SelectQuery in context of the specified ORM-related entity
	 * @return void
	 */
	function fill(SelectQuery $selectQuery, EntityQueryBuilder $entityQueryBuilder);
}

?>