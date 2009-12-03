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
 * Represents a SQL SELECT query.
 *
 * Consider the following methods to build a basic select query:
 * - SelectQuery::from()
 * - SelectQuery::get()
 * - SelectQuery::where()
 *
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
	private $row;

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
	 * @var OrderChain
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
	 * SelectQuery static constructor
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
	 * Sets the query to eliminate duplicate rows from the result
	 *
	 * @return SelectQuery
	 */
	function setDistinct()
	{
		$this->distinct = true;

		return $this;
	}

	/**
	 * Sets the condition for rows that should be selected
	 *
	 * Only rows for which this expression returns true will be selected.
	 *
	 * @param IExpression $condition condition to be applied when selected rows
	 *
	 * @return SelectQuery
	 */
	function setCondition(IExpression $condition)
	{
		$this->condition = $condition;

		return $this;
	}

	/**
	 * Sets the condition for rows that should be selected
	 *
	 * Only rows for which this expression returns true will be selected.
	 *
	 * @param IExpression $condition condition to be applied when selected rows
	 *
	 * @see SelectQuery::setCondition()
	 *
	 * @return SelectQuery itself
	 */
	function where(IExpression $condition)
	{
		$this->setCondition($condition);

		return $this;
	}

	/**
	 * Gets the condition for rows that should be deleted, if set.
	 *
	 * @return IExpression|null
	 */
	function getCondition()
	{
		return $this->condition;
	}

	/**
	 * Appends the expression to a SELECT list that form the output rows of the statement.
	 *
	 * The expression usually refers to a column of a table, or to a function.
	 *
	 * Consider using:
	 * - SqlColumn to refer to table columns
	 * - SqlFunction to call functions
	 * - AliasedSqlValueExpression to label the column of the output rows
	 *
	 * Example:
	 * @code
	 * // get the primary key
	 * $query->get(new SqlColumn('id', 'my_table'));
	 *
	 * // get the number of rows and label the result as "count_result"
	 * $query->get(
	 * 	new AliasedSqlValueExpression(
	 * 		new SqlFunction('COUNT', 'id'),
	 * 		'count_result'
	 * 	)
	 * );
	 * @endcode
	 *
	 * @param ISqlValueExpression $expression a boolean expression that would form the output rows
	 *
	 * @return SelectQuery itself
	 */
	function get(ISqlValueExpression $expression)
	{
		$this->get->append($expression);

		return $this;
	}

	/**
	 * Appends the list of expressions to a SELECT list that form the output rows of the statement
	 *
	 * @see SelectQuery::get()
	 * @param array $fields array of ISqlValueExpression
	 * @return SelectQuery itself
	 */
	function getFields(array $fields)
	{
		foreach ($fields as $field) {
			$this->get($field);
		}

		return $this;
	}

	/**
	 * Appends the table (optinally labeled) to the list of sources for selection.
	 *
	 * This method is a shorthand for the following:
	 * @code
	 * $query->addSource(
	 * 	new SelectQuerySource(
	 * 		new AliasedSqlValueExpression(
	 * 			new SqlIdentifier('my_table'),
	 * 			'my_table_alias'
	 * 		)
	 * 	)
	 * );
	 * @endcode
	 *
	 * @return SelectQuery itself
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
	 * Appends the expression to the list of sources for selection.
	 *
	 * Source is represented as ISqlValueExpression and can be the following:
	 * - SqlIdentifier to specify tables
	 * - SqlFunction to aggregate or produce the results
	 * - Expression to produce the results
	 * - SelectQuery as a sub-query
	 * - AliasedSqlValueExpression to label the source
	 *
	 * @return SelectQuery itself
	 */
	function addSource(SelectQuerySource $source)
	{
		$this->sources->append($source);

		return $this;
	}

	/**
	 * Appends the join clause to the last-added source for selection
	 *
	 * @return SelectQuery itself
	 */
	function join(SqlJoin $join)
	{
		Assert::isFalse($this->sources->isEmpty(), 'set any source before joining');

		$this->sources->getLast()->join($join);

		return $this;
	}

	/**
	 * Appends the expression(s) that will condense into a single row all selected rows that share
	 * the same values for the grouped expressions.
	 *
	 * Multiple arguments implementing ISqlValueExpression are accepted.
	 *
	 * Expression can be an input column name, or the name or ordinal number of an output column
	 * (SELECT list item), or an arbitrary expression formed from input-column value.
	 *
	 * @param ISqlValueExpression ...
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
	 * Sets the expression that eliminates group rows that do not satisfy the condition.
	 *
	 * @param IExpression $expression expression to use
	 *
	 * @return SelectQuery itself
	 */
	function having(IExpression $expression = null)
	{
		$this->having = $expression;

		return $this;
	}

	/**
	 * Appends the list of expressions that will be used when sorting the resulting rows.
	 *
	 * Multiple arguments implementing OrderBy are accepted.
	 *
	 * @param OrderBy ...
	 * @return SelectQuery itself
	 */
	function orderBy(OrderBy $expression)
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

	function toDialectString(IDialect $dialect)
	{
		$querySlices = array();

		$querySlices[] = 'SELECT';
		if ($this->distinct) {
			$querySlices[] = 'DISTINCT';
		}

		$querySlices[] = $this->row->toDialectString($dialect);

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

	function getPlaceholderValues(IDialect $dialect)
	{
		return array ();
	}
}

?>