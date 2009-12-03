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
 * Projection that will condense into a single row all selected rows that share
 * the same values for the grouped expressions.
 *
 * Example:
 * @code
 * // SELECT product_name
 * // FROM product
 * // WHERE product_id = 1
 * // GROUP BY name, in_stock
 * $query =
 * 	EntityQuery::create(Product::orm())
 * 		->get(Projection::groupBy("name"))
 * 		->get(Projection::groupBy("inStock"));
 * @code
 *
 * @ingroup Orm_Query_Projections
 */
final class GroupByPropertyProjection extends PropertyProjection
{
	protected function fillPropertyField($field, SelectQuery $selectQuery, EntityQueryBuilder $entityQueryBuilder)
	{
		$entityQueryBuilder->registerIdentifier($field);

		$selectQuery->groupBy(
			new SqlColumn($field, $entityQueryBuilder->getAlias())
		);
	}
}

?>