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
 * Represetns an order expression
 * @ingroup Dal_DB_Sql
 */
class SqlOrderExpression implements ISqlCastable
{
	/**
	 * @var ISqlValueExpression
	 */
	private $expression;

	/**
	 * @var SqlOrderDirection
	 */
	private $direction;

	/**
	 * Creates an instance of {@link SqlOrderExpression}
	 * @return SqlOrderExpression
	 */
	static function create(ISqlValueExpression $field, SqlOrderDirection $direction = null)
	{
		return new self ($field, $direction);
	}

	function __construct(ISqlValueExpression $field, SqlOrderDirection $direction = null)
	{
		$this->expression = $field;
		$this->direction = $direction
			? $direction
			: SqlOrderDirection::none();
	}

	/**
	 * Determines whether the direction of the expression is ASC
	 * @return boolean
	 */
	function isAsc()
	{
		return $this->direction->is(SqlOrderDirection::ASC);
	}

	/**
	 * Determines whether the direction of the expression is DESC
	 * @return boolean
	 */
	function isDesc()
	{
		return $this->direction->is(SqlOrderDirection::DESC);
	}

	/**
	 * Sets the direction of order expression to ASC
	 * @return SqlOrderExpression an object itself
	 */
	function asc()
	{
		$this->direction->setValue(SqlOrderDirection::ASC);

		return $this;
	}

	/**
	 * Sets the direction of order expression to DESC
	 * @return SqlOrderExpression an object itself
	 */
	function desc()
	{
		$this->direction->setValue(SqlOrderDirection::DESC);

		return $this;
	}

	/**
	 * Drops the direction of order expression to the default
	 * @return SqlOrderExpression an object itself
	 */
	function none()
	{
		$this->direction->setValue(SqlOrderDirection::NONE);

		return $this;
	}

	/**
	 * Reverts the direction of order expression. ASC becomes DESC, DESC and NONE becomes ASC
	 * @return SqlOrderExpression an object itself
	 */
	function revert()
	{
		$this->direction->setValue(
			$this->isAsc()
				? SqlOrderDirection::DESC
				: SqlOrderDirection::ASC
		);

		return $this;
	}

	/**
	 * Casts an object to the SQL dialect string
	 * @return string
	 */
	function toDialectString(IDialect $dialect)
	{
		return
			  $this->expression->toDialectString($dialect)
			. ' '
			. $this->direction->toDialectString($dialect);
	}

}

?>