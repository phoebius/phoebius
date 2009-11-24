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
 * Represents an order expression
 * @ingroup Dal_DB_Sql
 */
final class OrderBy implements ISubjective, ISqlCastable
{
	/**
	 * @var mixed
	 */
	private $expression;

	/**
	 * @var OrderDirection
	 */
	private $direction;

	/**
	 * @return OrderBy
	 */
	static function asc($expression)
	{
		return new self ($expression, OrderDirection::asc());
	}

	/**
	 * @return OrderBy
	 */
	static function desc($expression)
	{
		return new self ($expression, OrderDirection::desc());
	}

	function __construct($expression, OrderDirection $direction = null)
	{
		$this->expression = $expression;
		$this->direction =
			$direction
				? $direction
				: OrderDirection::none();
	}

	function getExpression()
	{
		return $this->expression;
	}

	function getDirection()
	{
		return $this->direction;
	}

	/**
	 * Determines whether the direction of the expression is ASC
	 * @return boolean
	 */
	function isAsc()
	{
		return $this->direction->is(OrderDirection::ASC);
	}

	/**
	 * Determines whether the direction of the expression is DESC
	 * @return boolean
	 */
	function isDesc()
	{
		return $this->direction->is(OrderDirection::DESC);
	}

	/**
	 * Sets the direction of order expression to ASC
	 * @return OrderBy an object itself
	 */
	function setAsc()
	{
		$this->direction->setValue(OrderDirection::ASC);

		return $this;
	}

	/**
	 * Sets the direction of order expression to DESC
	 * @return OrderBy an object itself
	 */
	function setDesc()
	{
		$this->direction->setValue(OrderDirection::DESC);

		return $this;
	}

	/**
	 * Drops the direction of order expression to the default
	 * @return OrderBy an object itself
	 */
	function none()
	{
		$this->direction->setValue(OrderDirection::NONE);

		return $this;
	}

	function toSubjected(ISubjectivity $object)
	{
		return new self(
			$object->subject($this->expression),
			new OrderDirection($this->direction->getValue())
		);
	}

	function toDialectString(IDialect $dialect)
	{
		return
			  $this->getExpression()->toDialectString($dialect)
			. (
				$this->direction->isNot(OrderDirection::NONE)
					? ' ' . $this->getDirection()->toDialectString($dialect)
					: ''
			);
	}
}

?>