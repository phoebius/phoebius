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
 * Projection for selecting the concrete property of an entity
 *
 * Example:
 * @code
 * // SELECT product_name
 * // FROM product
 * // WHERE product_id = 1
 * $query =
 * 	EntityQuery::create(Product::orm())
 * 		->get(Projection::property("name"));
 * @endcode
 *
 * @ingroup Orm_Query_Projections
 */
class PropertyProjection implements IProjection
{
	private $property;

	/**
	 * @param string $property name of a property to select
	 */
	function __construct($property)
	{
		$this->property = $property;
	}

	function fill(SelectQuery $selectQuery, EntityQueryBuilder $entityQueryBuilder)
	{
		$property = $entityQueryBuilder->getEntity()->getLogicalSchema()->getProperty($this->property);

		foreach ($property->getFields() as $field) {
			$this->fillPropertyField($field, $selectQuery, $entityQueryBuilder);
		}

		$selectQuery->get($this->getValueExpression($entityQueryBuilder));
	}

	/**
	 * Adds the table field to the SELECT list
	 *
	 * @param string $field
	 * @param SelectQuery $selectQuery
	 * @param EntityQueryBuilder $entityQueryBuilder
	 */
	protected function fillPropertyField(
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

	/**
	 * Converts value expression to be appended to SELECT list
	 * @param EntityQueryBuilder $builder
	 * @return ISqlValueExpression
	 */
	protected function getValueExpression(EntityQueryBuilder $builder)
	{
		return
			new AliasedSqlValueExpression(
				$builder->subject($this->property)
			);
	}
}

?>