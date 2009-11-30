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
 * Represents an expression that is used in ordering the resulting database rows
 * @ingroup Dal_DB_Sql
 */
final class OrderBy implements ISubjective, ISqlValueExpression
{
	/**
	 * @var mixed
	 */
	private $expression;

	/**
	 * @var OrderDirection|null
	 */
	private $direction;

	/**
	 * Creates and OrderBy expression with ascending sorting logic
	 *
	 * @param mixed $expression expression to use in sorting
	 *
	 * @return OrderBy
	 */
	static function asc($expression)
	{
		return new self ($expression, OrderDirection::asc());
	}

	/**
	 * Creates and OrderBy expression with descending sorting logic
	 *
	 * @param mixed $expression expression to use in sorting
	 *
	 * @return OrderBy
	 */
	static function desc($expression)
	{
		return new self ($expression, OrderDirection::desc());
	}

	function __construct($expression, OrderDirection $direction = null)
	{
		$this->expression = $expression;
		$this->direction = $direction;
	}

	/**
	 * Determines whether the direction of the expression is ascending
	 * @return boolean
	 */
	function isAsc()
	{
		return $this->direction->is(OrderDirection::ASC);
	}

	/**
	 * Determines whether the direction of the expression is descending
	 * @return boolean
	 */
	function isDesc()
	{
		return $this->direction->is(OrderDirection::DESC);
	}

	/**
	 * Sets the ascending direction of order the expression
	 * @return OrderBy itself
	 */
	function setAsc()
	{
		$this->direction = OrderDirection::asc();

		return $this;
	}

	/**
	 * Sets the descending direction of order the expression
	 * @return OrderBy itself
	 */
	function setDesc()
	{
		$this->direction = OrderDirection::desc();

		return $this;
	}

	function toSubjected(ISubjectivity $object)
	{
		return new self(
			$object->subject($this->expression),
			$this->direction
				? new OrderDirection($this->direction->getValue())
				: null
		);
	}

	function toDialectString(IDialect $dialect)
	{
		return
			  $this->getExpression()->toDialectString($dialect)
			. (
				$this->direction
					? ' ' . $this->getDirection()->toDialectString($dialect)
					: ''
			);
	}
}

?>