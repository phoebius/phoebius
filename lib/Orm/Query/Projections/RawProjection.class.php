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
 * Represents raw, optionally aliased projection. This projection appned ISubjective expression
 * into the into a SELECT list that form the output rows of the statement.
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
class RawProjection implements IProjection
{
	private $expression;
	private $alias;

	/**
	 * @param mixed $expression value expression to be selected
	 * @param string $alias alias for value expression
	 */
	function __construct($expression, $alias = null)
	{
		Assert::isScalarOrNull($alias);

		$this->expression = $expression;
		$this->alias = $alias;
	}

	/**
	 * Gets the label for expression to select
	 *
	 * @return string|null
	 */
	function getAlias()
	{
		return $this->alias;
	}

	function fill(SelectQuery $selectQuery, EntityQueryBuilder $entityQueryBuilder)
	{
		$selectQuery->get($this->getValueExpression($entityQueryBuilder));
	}

	/**
	 * Converts value expression to be appended to SELECT list
	 * @param EntityQueryBuilder $builder
	 * @return ISqlValueExpression
	 */
	protected function getValueExpression(EntityQueryBuilder $builder)
	{
		$builder->registerIdentifier($this->alias);

		return
			new AliasedSqlValueExpression(
				$builder->subject($this->getExpression()),
				$this->alias
			);
	}
}

?>