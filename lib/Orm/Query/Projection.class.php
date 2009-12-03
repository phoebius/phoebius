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
 * Helper class to create projections (aka IProjection) of various types.
 *
 * A realisting example (taken from PostgreSQL documentation):
 * @code
 * SELECT product_id, p.name, (sum(s.units) * (p.price - p.cost)) AS profit
 *  FROM products p LEFT JOIN sales s USING (product_id)
 *  WHERE s.date > CURRENT_DATE
 *  GROUP BY product_id, p.name, p.price, p.cost
 *  HAVING sum(p.price * s.units) > 5000;
 * @endcode
 *
 * Example:
 * @code
 * $query = new EntityQuery(Products::orm());
 * $query->get(Projection::property("id"));
 * $query->get(Projection::property("name"));
 * $query->get(
 * 	new RawProjection(
 * 		Expression::mul(
 * 			Projection::aggr("sum", "sales.units"),
 * 			Expression::sub("price", "cost")
 * 		),
 * 		"profit"
 * );
 * $query->where(
 * 	Expression::gt("sales.date", time())
 * );
 *
 * $query->get(
 * 	Projection::groupByProperty("id"),
 *  Projection::groupByProperty("name"),
 *  Projection::groupByProperty("price"),
 *  Projection::groupByProperty("cost")
 * );
 *
 * $query->get(
 * 	Projection::having(
 * 		Expression::gt(
 * 			new SqlFunction("sum", Expression::mul("price", "sales.units")),
 * 			5000
 * 		)
 * 	)
 * );
 * @endcode
 *
 * @ingroup Orm_Query
 */
final class Projection extends StaticClass
{
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
	 * @param string|IDaoRelated|IQueriable $entity
	 *
	 * @return EntityProjection
	 */
	static function entity($entity)
	{
		return new EntityProjection(
			self::getQueriable($entity)
		);
	}

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
	 * @code
	 *
	 * @param string $property name of the property
	 * @param string $alias optional label for the property value
	 *
	 * @warning composite properties are not supported
	 *
	 * @return RawProjection
	 */
	static function property($property, $alias = null)
	{
		return new RawProjection($property, $alias);
	}

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
	 * @param mixed $group reference to an a property or expression
	 *
	 * @return IProjection
	 */
	static function groupBy($group)
	{
		return new GroupByPropertyProjection($group);
	}

	/**
	 * Sets the expression that eliminates group rows that do not satisfy the condition.
	 *
	 * Example:
	 * @code
	 * // HAVING sum(p.price * s.units) > 5000;
	 * $query->get(
	 * 	Projection::having(
	 * 		Expression::gt(
	 * 			new SqlFunction("sum", Expression::mul("price", "sales.units")),
	 * 			5000
	 * 		)
	 * 	)
	 * );
	 * @code
	 *
	 * @param IExpression $expression
	 *
	 * @return HavingProjection
	 */
	static function having(IExpression $expression)
	{
		return new HavingProjection($expression);
	}

	/**
	 * Projection that counts the resulting rows by the property and optionally labels the result
	 *
	 * @param string $property property to be used for aggregation
	 * @param string $alias optional label for the result of the aggregator
	 *
	 * @return CountProjection
	 */
	static function count($property, $alias = null)
	{
		return new CountProjection($property, $alias);
	}

	/**
	 * Projection for counting the raw number of resulting rows (using the primary key)
	 *
	 * @param string $alias optional label for the result of the aggregator
	 *
	 * @return RowCountProjection
	 */
	static function rowCount($alias = null)
	{
		return new RowCountProjection($alias);
	}

	/**
	 * Projection invokes the count aggregate for all distinct non-null values of the specified
	 * property or expression found in the input rows
	 *
	 * @param string $property property to be used for aggregation
	 * @param string $alias optional label for the result of the aggregator
	 *
	 * @return DistinctCountProjection
	 */
	static function distinctCount($property, $alias = null)
	{
		return new DistinctCountProjection($property, $alias);
	}

	/**
	 * Projection invokes the specified aggregate across all input rows for which the given
	 * property or expression yield non-null values
	 *
	 * @param string $func name of the aggregate
	 * @param string $property property to be used for aggregation
	 * @param string $alias optional label for the result of the aggregator
	 *
	 * @return AggrProjection
	 */
	static function aggr($func, $property, $alias = null)
	{
		return new AggrProjection($func, $property, $alias);
	}

	/**
	 * @return IQueryable
	 */
	private static function getQueriable($class)
	{
		if (is_object($class) && $class instanceof IOrmRelated) {
			$class = get_class($class);
		}

		if (is_scalar($class) && TypeUtils::isChild($class, 'IOrmRelated')) {
			$class = call_user_func(array($class, 'orm'));
		}

		return $class;
	}
}

?>