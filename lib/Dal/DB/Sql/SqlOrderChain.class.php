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
 * Represents the SQL ORDER BY chain
 * @ingroup Dal_DB_Sql
 */
final class SqlOrderChain extends TypedValueList implements ISqlCastable
{
	/**
	 * Create an instance of {@link SqlOrderChain}
	 * @var array of {@link SqlOrderExpression}
	 * @return SqlOrderChain
	 */
	static function create(array $expressions = array())
	{
		return new self ($expressions);
	}

	/**
	 * Adds a {@link SqlOrderExpression} order expression
	 * @return SqlOrderChain
	 */
	function add(SqlOrderExpression $expression)
	{
		$this->append($expression);
	}

	/**
	 * Drops the ascending logic in all order expressions and sets the ASC logic to the last one
	 * expression
	 * @return SqlOrderChain an object itself
	 */
	function asc()
	{
		if ($this->getCount()) {
			foreach ($this->getList() as $expression) {
				$expression->setNone();
			}

			end($this->getList())->setAsc();
		}

		return $this;
	}

	/**
	 * Drops the ascending logic in all order expressions and sets the DESC logic to the last one
	 * expression
	 * @return SqlOrderChain
	 */
	function desc()
	{
		if ($this->getCount()) {
			foreach ($this->getList() as $expression) {
				$expression->setNone();
			}

			end($this->getList())->setDesc();
		}

		return $this;
	}

	/**
	 * Casts an object to the SQL dialect string
	 * @return string
	 */
	function toDialectString(IDialect $dialect)
	{
		$compiledSlices = array();

		if ($this->getCount() > 0) {
			foreach($this->getList() as $orderByExpression) {
				$compiledSlices[] = $orderByExpression->toDialectString($dialect);
			}
		}

		$compiledString = 'ORDER BY ' . join(', ', $compiledSlices);

		return $compiledString;
	}

	/**
	 * Determines whether the specified value is of valid type supported by the list implementation
	 * @return boolean
	 */
	protected function isValueOfValidType($value)
	{
		return ($value instanceof SqlOrderExpression);
	}
}

?>