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
 * @example SelectQuery.php
 * Using SelectQuery
 */

/**
 * Represents a simple select query
 * @ingroup Dal_DB_Query
 * @todo open a wider public API for creating nested joins (specify {@link SqlJoinMethod} manually
 */
class SelectQuery implements ISqlSelectQuery, ISqlValueExpression, ISelectQuerySource
{
	/**
	 * @var array of ISelectiveExpression
	 */
	private $fields = array();

	/**
	 * @var array of SelectQuerySource
	 */
	private $sources = array();

	/**
	 * @var IDalExpression|null
	 */
	private $expression;

	/**
	 * @var SqlOrderChain
	 */
	private $orderByChain;

	/**
	 * @var array of ISqlValueExpression
	 */
	private $groupByExpressions = array();

	/**
	 * @var IDalExpression
	 */
	private $having;

	/**
	 * @var integer
	 */
	private $limit = 0;

	/**
	 * @var offset
	 */
	private $offset = 0;

	/**
	 * @var boolean
	 */
	private $distinct;

	/**
	 * Creates an instance of {@link SelectQuery} class
	 * @return SelectQuery
	 */
	static function create()
	{
		return new self;
	}

	function __construct()
	{
		$this->orderByChain = new SqlOrderChain();
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
	function setExpression(IDalExpression $logic)
	{
		$this->expression = $logic;

		return $this;
	}

	/**
	 * Gets the query condition (i.e. "WHERE" clause) or NULL if not yet set
	 * @return IDalExpression|null
	 */
	function getExpression()
	{
		return $this->expression;
	}

	/**
	 * Sets a scalar aliased field to be fetched by a query
	 * @param string $fieldName
	 * @param string $alias
	 * @param string $tableName
	 * @return SelectQuery an object itself
	 */
	function get($fieldName, $alias = null, $tableName = null)
	{
		Assert::isScalarOrNull($alias);

		$this->fields[] = array(new SqlColumn($fieldName, $tableName), $alias);

		return $this;
	}

	/**
	 * @return SelectQuery an object itself
	 */
	function getFields(array $fields)
	{
		foreach ($fields as $field) {
			$this->get($field);
		}

		return $this;
	}

	/**
	 * Sets the expression to be fetched by a query
	 * @param ISqlValueExpression $expression
	 * @param scalar $alias
	 * @return SelectQuery an object itself
	 */
	function getExpr(ISqlValueExpression $expression, $alias = null)
	{
		Assert::isScalarOrNull($alias);

		$this->fields[] = array($expression, $alias);

		return $this;
	}

	/**
	 * Sets the source to which the select query should be applied
	 * @param scalar $tableName
	 * @param scalar $tableAlias
	 * @return SelectQuery
	 */
	function from($tableName, $tableAlias = null)
	{
		$this->sources[] = new TableSelectQuerySource($tableName, $tableAlias);

		return $this;
	}

	/**
	 * Sets the complex source to which the select query should be applied
	 * @param ISelectQuerySource $target
	 * @param scalar $alias
	 * @return SelectQuery
	 */
	function fromComplex(ISelectQuerySource $target, $alias = null)
	{
		$this->sources[] = new ComplexSelectQuerySource($target, $alias);

		return $this;
	}

	function join(SqlJoin $join)
	{
		Assert::isNotEmpty($this->sources, 'set any target before joining');

		end($this->sources)->join($join);

		return $this;
	}

	/**
	 * Drops grouping schema and adds a grouping element
	 * @param ISqlValueExpression $expression
	 * @return SelectQuery an object itself
	 */
	function groupBy(ISqlValueExpression $expression)
	{
		$this->dropGroupBy()->andGroupBy($expression);

		return $this;
	}

	/**
	 * Adds a grouping element
	 * @param ISqlValueExpression $expression
	 * @return SelectQuery an object itself
	 */
	function andGroupBy(ISqlValueExpression $expression)
	{
		$this->groupByExpressions[] = $expression;

		return $this;
	}

	/**
	 * Drops a grouping list
	 * @return SelectQuery an object itself
	 */
	function dropGroupBy()
	{
		$this->groupByExpressions = array();

		return $this;
	}

	/**
	 * Adds a having for logical expression
	 * @return SelectQuery an object itself
	 */
	function having(IDalExpression $expression)
	{
		$this->having = $expression;

		return $this;
	}

	/**
	 * Drops ORDERBY list and adds an order expression
	 * @return SelectQuery an object itself
	 */
	function orderBy(ISqlValueExpression $expression, SqlOrderDirection $direction = null)
	{
		$this->dropOrderBy();
		$this->andOrderBy($expression, $direction);

		return $this;
	}

	/**
	 * Adds an order expression
	 * @return SelectQuery an object itself
	 */
	function andOrderBy(ISqlValueExpression $expression, SqlOrderDirection $direction = null)
	{
		$this->orderByChain->add(new SqlOrderExpression($expression, $direction));

		return $this;
	}

	/**
	 * Drops the set of order expressions
	 * @return SelectQuery an object itself
	 */
	function dropOrderBy()
	{
		$this->orderByChain->dropList();

		return $this;
	}

	/**
	 * Sets a limit for row selection
	 * @param integer $limit positive integer
	 * @return SelectQuery an object itself
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
	 * @return SelectQuery an object itself
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
	 * @return SelectQuery an object itself
	 */
	function dropOffset()
	{
		$this->offset = 0;

		return $this;
	}

	/**
	 * Casts an object to the SQL dialect string
	 * @return string
	 */
	function toDialectString(IDialect $dialect)
	{
		$querySlices = array();

		$querySlices[] = 'SELECT';
		if ($this->distinct) {
			$querySlices[] = 'DISTINCT';
		}
		$querySlices[] = $this->compileFields($dialect);

		if (!empty($this->sources)) {
			$querySlices[] = 'FROM';
			$querySlices[] = $this->compileTargets($dialect);
		}

		// WHERE
		if ($this->expression) {
			$expressionAsString = $this->expression->toDialectString($dialect);
			if (!empty($expressionAsString)) {
				$querySlices[] = 'WHERE';
				$querySlices[] = $expressionAsString;
			}
		}

		// GROUP BY
		if (!empty($this->groupByExpressions)) {
			$querySlices[] = 'GROUP BY';
			foreach ($this->groupByExpressions as $groupByExpression) {
				$querySlices[] = $groupByExpression->toDialectString($dialect);
			}
		}

		// HAVING
		if (!empty($this->having)) {
			$querySlices[] = 'HAVING';
			$querySlices[] = $this->having->toDialectString($dialect);
		}

		if ($this->orderByChain->getCount()) {
			$querySlices[] = $this->orderByChain->toDialectString($dialect);
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

	/**
	 * @see ISqlQuery::getCastedParameters()
	 *
	 * @param IDialect $dialect
	 * @return array
	 */
	function getCastedParameters(IDialect $dialect)
	{
		return array ();
	}

	/**
	 * @return string
	 */
	private function compileTargets(IDialect $dialect)
	{
		$compiledTargets = array();
		foreach ($this->sources as $target) {
			$compiledTargets[] =  $target->toDialectString($dialect);
		}

		$compiledTargetsString = join(', ', $compiledTargets);
		return $compiledTargetsString;
	}

	/**
	 * @return string
	 */
	private function compileFields(IDialect $dialect)
	{
		$compiledFieldList = array();
		foreach ($this->fields as $field) {
			$compiledField = array();
			$fieldObject = reset($field);
			$compiledField[] = $fieldObject->toDialectString($dialect);

			$alias = end($field);
			if (!empty($alias)) {
				$compiledField[] = 'AS';
				$compiledField[] = $dialect->quoteIdentifier($alias);
			}

			$compiledFieldList[] = join(' ', $compiledField);
		}

		$compiledFieldString = join(', ', $compiledFieldList);
		return $compiledFieldString;
	}

}

?>