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
 * Projection invokes the specified aggregate across all input rows for which the given
 * property or expression yield non-null values
 *
 * @ingroup Orm_Query_Projections
 */
class AggrProjection implements IProjection
{
	private $func;
	protected $expression;
	protected $alias;

	/**
	 * @param string $func name of the aggregate
	 * @param string $expression property to be used for aggregation
	 * @param string $alias optional label for the result of the aggregator
	 */
	function __construct($func, $expression = null, $alias = null)
	{
		Assert::isScalar($func);

		$this->func = $func;

		$this->expression = $expression;
		$this->alias = $alias;
	}

	/**
	 * Gets the name of the aggregates
	 * @return string
	 */
	function getFunc()
	{
		return $this->func;
	}

	function fill(SelectQuery $selectQuery, EntityQueryBuilder $entityQueryBuilder)
	{
		$selectQuery->get($this->getSqlFunction($entityQueryBuilder));
	}

	/**
	 * Create a SQLFunction with the expression as the argument
	 *
	 * @param EntityQuery $entityQuery
	 * @return SqlFunction
	 */
	protected function getSqlFunction(EntityQueryBuilder $entityQueryBuilder)
	{
		return
			new AliasedSqlValueExpression(
				new SqlFunction(
					$this->getFunc(),
					$entityQueryBuilder->subject($this->expression)
				),
				$this->alias
			);
	}
}

?>