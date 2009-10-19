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
 * Represents a unary prefix expression
 * @ingroup Core_Expression
 */
class PrefixUnaryExpression implements IExpression
{
	/**
	 * @var mixed
	 */
	private $subject;

	/**
	 * @var PrefixUnaryLogicalOperator
	 */
	private $logic;

	function __construct(PrefixUnaryLogicalOperator $logic, $subject)
	{
		$this->logic = $logic;
		$this->subject = $subject;
	}

	/**
	 * @return mixed
	 */
	function getSubject()
	{
		return $this->subject;
	}

	/**
	 * @return PrefixUnaryLogicalOperator
	 */
	function getLogicalOperator()
	{
		return $this->logic;
	}

	/**
	 * @return PrefixUnaryExpression
	 */
	function toExpression(IExpressionSubjectConverter $converter)
	{
		return new self(
			$this->logic,
			$converter->convert($this->subject, $this)
		);
	}

	/**
	 * @return BinaryDalExpression
	 */
	function toDalExpression()
	{
		return new PrefixUnaryDalExpression($this);
	}

	/**
	 * @return ExpressionType
	 */
	function getExpressionType()
	{
		return new ExpressionType(ExpressionType::PREFIX_UNARY);
	}
}

?>