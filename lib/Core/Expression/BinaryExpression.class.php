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
 * Represents binary expression
 *
 * SQL example:
 * @code
 * // "id" = 1
 * Expression::eq("id", 1);
 * @endcode
 *
 * @ingroup Core_Expression
 */
class BinaryExpression implements IExpression
{
	/**
	 * @var mixed
	 */
	private $subject;

	/**
	 * @var mixed
	 */
	private $value;

	/**
	 * @var BinaryLogicalOperator
	 */
	private $logic;

	function __construct($subject, BinaryLogicalOperator $logic, $value)
	{
		$this->subject = $subject;
		$this->logic = $logic;
		$this->value = $value;
	}

	/**
	 * @return mixed
	 */
	function getSubject()
	{
		return $this->subject;
	}

	/**
	 * @return mixed
	 */
	function getValue()
	{
		return $this->value;
	}

	/**
	 * @return BinaryLogicalOperator
	 */
	function getLogicalOperator()
	{
		return $this->logic;
	}

	function toExpression(IExpressionSubjectConverter $converter)
	{
		return new self(
			$converter->convert($this->subject, $this),
			$this->logic,
			$converter->convert($this->value, $this)
		);
	}

	function toDalExpression()
	{
		return new BinaryDalExpression($this);
	}
}

?>