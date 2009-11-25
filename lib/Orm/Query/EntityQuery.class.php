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
 * LINQ to OrmEntity
 *
 * @example
 * @code
 * $entitySetQuery =
 * 	EntityQuery::create(MyEntity::orm())
 * 		->get(MyEntity::orm())
 * 		->where(
 *			Expression::between(
 *				'time',
 *				Date::create('-1 day')
 *				Date::create('+1 day')
 *			)
 * 		);
 * @endcode
 *
 * TODO:
 * # implement IOrmEntityAccessor helper methods
 *
 * @ingroup Orm_Query
 */
final class EntityQuery implements ISqlSelectQuery
{
	/**
	 * @var IQueryable
	 */
	private $entity;

	/**
	 * @var ProjectionChain
	 */
	private $projection;

	/**
	 * @var boolean
	 */
	private $distinct;

	/**
	 * @var IExpression|null
	 */
	private $condition;

	/**
	 * @var OrderChain
	 */
	private $order;

	/**
	 * @var integer
	 */
	private $limit = 0;

	/**
	 * @var offset
	 */
	private $offset = 0;

	/**
	 * @return EntityQuery
	 */
	static function create(IQueryable $entity)
	{
		return new self ($entity);
	}

	function __construct(IQueryable $entity)
	{
		$this->entity = $entity;
		$this->projection = new ProjectionChain;
		$this->order = new OrderChain;
	}

	function __clone()
	{
		$this->projection = clone $this->projection;
		$this->order = clone $this->order;
	}

	/**
	 * @return IQueryable
	 */
	function getEntity()
	{
		return $this->entity;
	}

	/**
	 * @return EntityQuery
	 */
	function setDistinct()
	{
		$this->distinct = true;

		return $this;
	}

	/**
	 * @param OrderBy ...
	 * @return EntityQuery an object itself
	 */
	function orderBy()
	{
		$expressions = func_get_args();
		foreach ($expressions as $expression) {
			$this->order->append($expression);
		}

		return $this;
	}

	/**
	 * Sets a limit for row selection
	 * @param integer $limit positive integer
	 * @return EntityQuery an object itself
	 */
	function setLimit($limit)
	{
		Assert::isPositiveInteger($limit);

		$this->limit = $limit;

		return $this;
	}

	/**
	 * Gets the limit for the row selection
	 * @return integer 0 if limit is not set, otherwise a positive integer
	 */
	function getLimit()
	{
		return $this->limit;
	}

	/**
	 * Drops a row selection limit
	 * @return EntityQuery an object itself
	 */
	function dropLimit()
	{
		$this->limit = 0;

		return $this;
	}

	/**
	 * Sets the offset for row selection
	 * @param integer $offset positive integer
	 * @return EntityQuery
	 */
	function setOffset($offset)
	{
		Assert::isPositiveInteger($offset);

		$this->offset = $offset;

		return $this;
	}

	/**
	 * Gets the offset for the row selection
	 * @return integet 0 if offset is not set, otherwise a positive integer
	 */
	function getOffset()
	{
		return $this->offset;
	}

	/**
	 * Drops a row selection offset
	 * @return EntityQuery an object itself
	 */
	function dropOffset()
	{
		$this->offset = 0;

		return $this;
	}

	/**
	 * @return EntityQuery
	 */
	function get(IProjection $projection)
	{
		$this->projection->append($projection);

		return $this;
	}

	/**
	 * @return EntityQuery
	 */
	function where(IExpression $condition)
	{
		$this->condition = $condition;

		return $this;
	}

	/**
	 * @return SelectQuery
	 */
	function toSelectQuery()
	{
		$selectQuery = new SelectQuery;
		$queryBuilder = new EntityQueryBuilder($this);

		if ($this->projection->isEmpty()) {
			Projection::entity($this->entity)->fill($selectQuery, $queryBuilder);
		}
		else {
			$this->projection->fill($selectQuery, $queryBuilder);
		}

		if ($this->condition) {
			$selectQuery->setCondition($this->condition->toSubjected($queryBuilder));
		}

		foreach ($this->order as $orderBy) {
			$selectQuery->orderBy($orderBy->toSubjected($queryBuilder));
		}

		$selectQuery->setLimit($this->limit);
		$selectQuery->setOffset($this->offset);

		foreach ($queryBuilder->getSelectQuerySources() as $source) {
			$selectQuery->addSource($source);
		}

		return $selectQuery;
	}

	function getList()
	{
		return $this->entity->getDao()->getListByQuery($this);
	}

	function getCastedParameters(IDialect $dialect)
	{
		return array ();
	}

	function toDialectString(IDialect $dialect)
	{
		return $this->toSelectQuery()->toDialectString($dialect);
	}
}

?>