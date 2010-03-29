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
 * API to fill the query:
 * - Projection as a shorthand for various projections
 * - EntityQuery::get() to add the projections to the projection chain
 * - Expression as a shorthand for various expression trees
 * - EntityQuery::where() to set the expression
 *
 * API to retrieve ORM-related entity objects:
 * - EntityQuery::getList() to retrieve lists of entities
 * - EntityQuery::getEntity() to retrieve a specific object
 * - EntityQuery::getProperty() to get the value of a specific cell
 *
 * API to retrieve raw data:
 * - EntityQuery::getCell(), EntityQuery::getCount() - raw cell values
 * - EntityQuery::getRow() to get the database row
 * - EntityQuery::getRows()
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
	 * EntityQuery static constructor
	 * @param IQueryable $entity entity we are going to query
	 * @return EntityQuery
	 */
	static function create(IQueryable $entity)
	{
		return new self ($entity);
	}

	/**
	 * @param IQueryable $entity entity we are going to query
	 */
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
		if ($this->condition) {
			$this->condition = clone $this->condition;
		}
	}

	/**
	 * Gets the entity we are going to query
	 * @return IQueryable
	 */
	function getQueryRoot()
	{
		return $this->entity;
	}

	/**
	 * Sets the query to eliminate duplicate rows from the result
	 * @return EntityQuery itself
	 */
	function setDistinct()
	{
		$this->distinct = true;

		return $this;
	}

	/**
	 * Appends the list of expressions that will be used when sorting the resulting rows.
	 *
	 * Multiple arguments implementing OrderBy are accepted.
	 *
	 * We don't know why OrderBY is not a part of a separate projection, so we follow
	 * the default behaviour of nhibernate
	 *
	 * @param OrderBy ...
	 * @return EntityQuery itself
	 */
	function orderBy()
	{
		$expressions = func_get_args();
		foreach ($expressions as $expression) {
			$this->order->append($expression);
		}

		return $this;
	}

	function setLimit($limit)
	{
		Assert::isPositiveInteger($limit);

		$this->limit = $limit;

		return $this;
	}

	function setOffset($offset)
	{
		Assert::isPositiveInteger($offset);

		$this->offset = $offset;

		return $this;
	}

	/**
	 * Appends an IProjection object to the projection chain that actually makes a selection
	 * expression that form the output rows of the statement.
	 *
	 * Example:
	 * @code
	 * // gets the number of entities within the database
	 * $count =
	 * 	EntityQuery::create(MyEntry::orm())
	 * 		->get(Projection::rowCount())
	 * 		->getCell();
	 * @endcode
	 *
	 * @param IProjection ...
	 *
	 * @return EntityQuery itlsef
	 */
	function get(IProjection $projection/*, ... */)
	{
		$args = func_get_args();
		foreach ($args as $arg) {
			$this->projection->append($arg);
		}

		return $this;
	}

	/**
	 * Appends an IProjection object to the projection chain that actually makes a selection
	 * expression that form the output rows of the statement.
	 *
	 * Example:
	 * @code
	 * // gets the number of entities within the database
	 * $count =
	 * 	EntityQuery::create(MyEntry::orm())
	 * 		->get(Projection::rowCount())
	 * 		->getCell();
	 * @endcode
	 *
	 * @param IProjection ...
	 *
	 * @return EntityQuery itlsef
	 */
	function addProjection(IProjection $projection)
	{
		$this->projection->append($projection);

		return $this;
	}

	/**
	 * Cleans the projection chain and appends an IProjection object to the projection chain
	 *  that actually makes a selection
	 * expression that form the output rows of the statement.
	 *
	 * Example:
	 * @code
	 * // gets the number of entities within the database
	 * $count =
	 * 	EntityQuery::create(MyEntry::orm())
	 * 		->get(Projection::rowCount())
	 * 		->getCell();
	 * @endcode
	 *
	 * @param IProjection ...
	 *
	 * @return EntityQuery itlsef
	 */
	function setProjection(IProjection $projection)
	{
		$this->dropProjection()->addProjection($projection);

		return $this;
	}

	/**
	 * Drops the projection chain
	 */
	function dropProjection()
	{
		$this->projection = new ProjectionChain;

		return $this;
	}

	/**
	 * Sets the condition for rows that should be selected
	 *
	 * Only rows for which this expression returns true will be selected.
	 *
	 * @param IExpression $condition condition to be applied when selected rows
	 *
	 * @return EntityQuery
	 */
	function where(IExpression $condition)
	{
		$this->condition = $condition;

		return $this;
	}

	/**
	 * Joins the condition for rows that should be selected using conjunction
	 *
	 * Only rows for which this expression returns true will be selected.
	 *
	 * @param IExpression $condition condition to be applied when selected rows
	 *
	 * @return EntityQuery
	 */
	function andWhere(IExpression $condition)
	{
		if ($this->condition) {
			$this->condition = Expression::conjunction($this->condition, $condition);
		}
		else {
			$this->condition = $condition;
		}

		return $this;
	}

	/**
	 * Joins the condition for rows that should be selected using disjunction
	 *
	 * Only rows for which this expression returns true will be selected.
	 *
	 * @param IExpression $condition condition to be applied when selected rows
	 *
	 * @return EntityQuery
	 */
	function orWhere(IExpression $condition)
	{
		if ($this->condition) {
			$this->condition = Expression::disjunction($this->condition, $condition);
		}
		else {
			$this->condition = $condition;
		}

		return $this;
	}

	/**
	 * Presents EntityQuery as SelectQuery object
	 *
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
		// preaparation
		$selectQuery = new SelectQuery;
		$queryBuilder = new EntityQueryBuilder($this->entity);

		// subjection
		$projection->fill($selectQuery, $queryBuilder);

		if ($this->condition) {
			$selectQuery->where(
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
	 * Converts EntityQuery to IExpresion object.
	 *
	 * Note that projections, and references to associated entities are useless and won't be
	 * presented in the resulting expression because required joins can only be specified
	 * as sources for selection
	 *
	 * @todo check whether we refer to encapsulants and avoid this
	 *
	 * @return IExpression
	 */
	function toExpression()
	{
		if (!$this->condition) {
			return new ExpressionChain;
		}

		return $this->condition->toSubjected(new EntityQueryBuilder($this->entity));
	}

	/**
	 * @return int
	 */
	function delete()
	{
		$deleteQuery = new DeleteQuery($this->entity->getPhysicalSchema()->getTable());

		$deleteQuery->setCondition($this->toExpression());

		$affected = $this->entity->getDao()->executeQuery($deleteQuery);

		return $affected;
	}

	/**
	 * Gets the ORM-related entity object. The object is obtained according to the current
	 * setting of EntityQuery and the FetchStrategy set inside DAO of the entity
	 * @return IdentifiableOrmEntity
	 */
	function getEntity()
	{
		return $this->entity->getDao()->getEntity($this);
	}

	/**
	 * Gets the plain database tuple. The tuple is obtained according to the current
	 * setting of EntityQuery and the FetchStrategy set inside DAO of the entity
	 * @return associative array that represents a tuple of raw database values
	 */
	function getRow()
	{
		return $this->entity->getDao()->getRow($this);
	}

	/**
	 * Gets a set of tuples of raw database values.
	 *
	 * @return array of associative arrays that represent a tuples of raw database values
	 */
	function getRows()
	{
		return $this->entity->getDao()->getRows($this);
	}

	/**
	 * Gets the plain database tuple cell. The cell is obtained according to the current
	 * setting of EntityQuery and the FetchStrategy set inside DAO of the entity
	 * @return scalar
	 */
	function getCell()
	{
		return $this->entity->getDao()->getCell($this);
	}

	/**
	 * Gets the set plain database tuples. The tuples are obtained according to the current
	 * setting of EntityQuery and the FetchStrategy set inside DAO of the entity
	 * @return array
	 */
	function getList()
	{
		return $this->entity->getDao()->getList($this);
	}

	/**
	 * Gets the number of entities presented in the database according to the current setting
	 * of EntityQuery
	 * @return int
	 */
	function getCount()
	{
		return $this->entity->getDao()->getCell(
			$this->makeSelect(
				Projection::rowCount()
			)
		);
	}

	/**
	 * Gets the value of the entity's property. The value is obtained according to the current
	 * setting of EntityQuery and the FetchStrategy set inside DAO of the entity
	 * @param string name of the property
	 * @return scalar
	 */
	function getProperty($property)
	{
		return $this->entity->getDao()->getProperty(
			$this->makeSelect(
				Projection::property($property)
			)
		);
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