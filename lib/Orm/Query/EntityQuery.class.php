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
 * Example:
 * @code
 * $entitySet =
 * 	EntityQuery::create(MyEntity::orm())
 * 		->where(
 *			Expression::between(
 *				'time',
 *				Date::create('-1 day')
 *				Date::create('+1 day')
 *			)
 * 		)
 * 		->getList();
 * @endcode
 *
 * @todo implement IOrmEntityAccessor helper methods
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
	function getPivot()
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
	 * We don't know why OrderBY is not a part of a separate projection, so we follow
	 * the default behaviour of nhibernate
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
		return $this->makeSelect(
			$this->projection->isEmpty()
				? Projection::entity($this->entity) // mostly-used default behaviour is getting the OrmEntities
				: $this->projection
		);
	}

	private function makeSelect(IProjection $projection)
	{
		$selectQuery = new SelectQuery;
		$queryBuilder = new EntityQueryBuilder($this);

		$projection->fill($selectQuery, $queryBuilder);

		if ($this->condition) {
			$selectQuery->setCondition(
				$this->condition->toSubjected($queryBuilder)
			);
		}

		foreach ($this->order as $orderBy) {
			$selectQuery->orderBy(
				$orderBy->toSubjected($queryBuilder)
			);
		}

		$selectQuery->setLimit($this->limit);
		$selectQuery->setOffset($this->offset);

		// now add all those sources that EntityQueryBuilder collected for us
		foreach ($queryBuilder->getSelectQuerySources() as $source) {
			$selectQuery->addSource($source);
		}

		return $selectQuery;
	}

	/**
	 * @return IExpression
	 */
	function toExpression()
	{
		if (!$this->condition) {
			return new ExpressionChain;
		}

		return $this->condition->toSubjected(new EntityQueryBuilder($this));
	}

	function getEntity()
	{
		return $this->entity->getDao()->getEntity($this);
	}

	function getRow()
	{
		return $this->entity->getDao()->getRow($this);
	}

	function getCell()
	{
		return $this->entity->getDao()->getCell($this);
	}

	function getList()
	{
		return $this->entity->getDao()->getList($this);
	}

	function getCount()
	{
		// delayed EntityQuery->SelectQuery cast
		$me = clone $this;
		$me->projection = Projection::rowCount();

		return $this->entity->getDao()->getCell($me);
	}

	function getProperty($property)
	{
		// delayed EntityQuery->SelectQuery cast
		$me = clone $this;
		$me->projection = Projection::property($property);

		return $this->entity->getDao()->getProperty($me);
	}

	function getPlaceholderValues(IDialect $dialect)
	{
		return array ();
	}

	function toDialectString(IDialect $dialect)
	{
		return $this->toSelectQuery()->toDialectString($dialect);
	}
}

?>