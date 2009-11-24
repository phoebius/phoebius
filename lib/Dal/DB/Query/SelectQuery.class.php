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
 * Represents a simple select query
 * @ingroup Dal_DB_Query
 */
class SelectQuery implements ISqlSelectQuery, ISqlValueExpression
{
	/**
	 * SELECT DISTINCT
	 * @var boolean
	 */
	private $distinct;

	/**
	 * SELECT ... FROM
	 * @var SqlValueExpressionArray
	 */
	private $get;

	/**
	 * FROM ...
	 * @var SqlValueExpressionArray
	 */
	private $sources;

	/**
	 * WHERE ...
	 * @var IExpression|null
	 */
	private $condition;

	/**
	 * ORDER BY ...
	 * @var SqlOrderChain
	 */
	private $order;

	/**
	 * GROUP BY ...
	 * @var SqlValueExpressionArray
	 */
	private $groups;

	/**
	 * HAVING ...
	 * @var IExpression|null
	 */
	private $having;

	/**
	 * LIMIT ...
	 * @var integer
	 */
	private $limit = 0;

	/**
	 * OFFSET ...
	 * @var offset
	 */
	private $offset = 0;

	/**
	 * Creates an instance of SelectQuery class
	 *
	 * @return SelectQuery
	 */
	static function create()
	{
		return new self;
	}

	function __construct()
	{
		$this->get = new SqlValueExpressionArray;
		$this->sources = new SqlValueExpressionArray;
		$this->order = new OrderChain;
		$this->groups = new SqlValueExpressionArray;
	}

	function __clone()
	{
		$this->get = clone $this->get;
		$this->sources = clone $this->sources;
		$this->order = clone $this->order;
		$this->groups = clone $this->groups;
	}

	/**
	 * @return SelectQuery
	 */
	function setDistinct()
	{
		$this->distinct = true;

		return $this;
	}

	/**
	 * Sets the query condition (for "WHERE" clause)
	 * @return SelectQuery
	 */
	function setCondition(IExpression $condition)
	{
		$this->condition = $condition;

		return $this;
	}

	/**
	 * Alias for SelectQuery::setCondition()
	 */
	function where(IExpression $condition)
	{
		$this->setCondition($condition);

		return $this;
	}

	/**
	 * Gets the query condition (i.e. "WHERE" clause) or NULL if not yet set
	 * @return IExpression|null
	 */
	function getCondition()
	{
		return $this->condition;
	}

	/**
	 * @return SelectQuery itself
	 */
	function get(ISqlValueExpression $expression)
	{
		$this->get->append($expression);

		return $this;
	}

	/**
	 * @return SelectQuery itself
	 */
	function getMultiple(array $fields)
	{
		foreach ($fields as $field) {
			$this->get($field);
		}

		return $this;
	}

	/**
	 * @return SelectQuery
	 */
	function from($table, $alias = null)
	{
		$this->addSource(
			new SelectQuerySource(
				new AliasedSqlValueExpression(
					new SqlIdentifier($table),
					$alias
				)
			)
		);

		return $this;
	}

	/**
	 * @return SelectQuery
	 */
	function addSource(SelectQuerySource $source)
	{
		$this->sources->append($source);

		return $this;
	}

	/**
	 * @return SelectQuery
	 */
	function join(SqlJoin $join)
	{
		Assert::isFalse($this->sources->isEmpty(), 'set any source before joining');

		$this->sources->getLast()->join($join);

		return $this;
	}

	/**
	 * @param SqlOrderExpression ...
	 * @return SelectQuery itself
	 */
	function groupBy(ISqlValueExpression $expression)
	{
		$expressions = func_get_args();
		foreach ($expressions as $expression) {
			$this->groups->append($expression);
		}

		return $this;
	}

	/**
	 * Adds a having for logical expression
	 * @return SelectQuery itself
	 */
	function having(IExpression $expression = null)
	{
		$this->having = $expression;

		return $this;
	}

	/**
	 * @param SqlOrderExpression ...
	 * @return SelectQuery itself
	 */
	function orderBy(SqlOrderExpression $expression)
	{
		$expressions = func_get_args();
		foreach ($expressions as $expression) {
			$this->order->add($expression);
		}

		return $this;
	}

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
	 * @return SelectQuery itself
	 */
	function dropLimit()
	{
		$this->limit = 0;

		return $this;
	}

	/**
	 * Sets the offset for row selection
	 * @param integer $offset positive integer
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
	 * @return SelectQuery itself
	 */
	function dropOffset()
	{
		$this->offset = 0;

		return $this;
	}

	function toDialectString(IDialect $dialect)
	{
		$querySlices = array();

		$querySlices[] = 'SELECT';
		if ($this->distinct) {
			$querySlices[] = 'DISTINCT';
		}

		$querySlices[] = $this->get->toDialectString($dialect);

		if (!$this->sources->isEmpty()) {
			$querySlices[] = 'FROM';
			$querySlices[] = $this->sources->toDialectString($dialect);
		}

		// WHERE
		if ($this->condition) {
			$querySlices[] = 'WHERE';
			$querySlices[] =  $this->condition->toDialectString($dialect);
		}

		// GROUP BY
		if (!$this->groups->isEmpty()) {
			$querySlices[] = 'GROUP BY';
			$querySlices[] = $this->groups->toDialectString($dialect);
		}

		// HAVING
		if ($this->having) {
			$querySlices[] = 'HAVING';
			$querySlices[] = $this->having->toDialectString($dialect);
		}

		if (!$this->order->isEmpty()) {
			$querySlices[] = $this->order->toDialectString($dialect);
		}

		if ($this->limit) {
			$querySlices[] = 'LIMIT';
			$querySlices[] = $this->limit;
		}

		if ($this->offset) {
			$querySlices[] = 'OFFSET';
			$querySlices[] = $this->offset;
		}

		$queryString = join(' ', $querySlices);
		return $queryString;
	}

	function getCastedParameters(IDialect $dialect)
	{
		return array ();
	}
}

?>