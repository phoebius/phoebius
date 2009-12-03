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
 * Projection for selecting all properties of the specified entity
 *
 * Example:
 * @code
 * // SELECT product_id, product_name, product_price
 * // FROM product
 * $query =
 * 	EntityQuery::create(Product::orm())
 * 		->get(Projection::entity("Product"));
 * @code
 *
 * @ingroup Orm_Query_Projections
 */
class EntityProjection implements IProjection
{
	/**
	 * @var IQueryable
	 */
	private $entity;

	/**
	 * @param IQueryable $entity entity to select
	 */
	function __construct(IQueryable $entity)
	{
		$this->entity = $entity;
	}

	function fill(SelectQuery $selectQuery, EntityQueryBuilder $entityQueryBuilder)
	{
		foreach ($this->entity->getPhysicalSchema()->getFields() as $field) {
			$this->fillField($field, $selectQuery, $entityQueryBuilder);
		}
	}

	/**
	 * Adds the table field to the SELECT list
	 *
	 * @param string $field
	 * @param SelectQuery $selectQuery
	 * @param EntityQueryBuilder $entityQueryBuilder
	 */
	protected function fillField(
			$field,
			SelectQuery $selectQuery,
			EntityQueryBuilder $entityQueryBuilder
		)
	{
		$entityQueryBuilder->registerIdentifier($field);

		$selectQuery->get(
			new SqlColumn($field, $entityQueryBuilder->getAlias())
		);
	}
}

?>