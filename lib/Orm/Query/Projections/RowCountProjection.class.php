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
 * Projection for counting the raw number of resulting rows (using the primary key)
 *
 * Example:
 * @code
 * // SELECT COUNT(entity_id) FROM ...
 * $query = new EntityQuery(Entity::orm());
 * $query->get(new RowCountProjection());
 * echo $query->getCell();
 * @endcode
 *
 * Hint: there is a shorthand for counting rows: EntityQuery::getCount()
 *
 * @ingroup Orm_Query_Projections
 */
final class RowCountProjection extends CountProjection
{
	/**
	 * @param string $alias optional label for the result of the aggregator
	 */
	function __construct($alias = null)
	{
		parent::__construct(null, $alias);
	}
}

?>