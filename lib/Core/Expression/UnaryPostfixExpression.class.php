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
 * Represents a postfix unary expression
 *
 * SQL example:
 * @code
 * // "id" IS NOT NULL
 * Expression::notNull("id");
 * @endcode
 *
 * @ingroup Core_Expression
 */
class UnaryPostfixExpression implements IExpression
{
	/**
	 * @var mixed
	 */
	private $subject;

	/**
	 * @var UnaryPostfixLogicalOperator
	 */
	private $logic;

	function __construct($subject, UnaryPostfixLogicalOperator $logic)
	{
		$this->subject = $subject;
		$this->logic = $logic;
	}

	/**
	 * @return mixed
	 */
	function getSubject()
	{
		return $this->subject;
	}

	/**
	 * @return UnaryPostfixLogicalOperator
	 */
	function getLogicalOperator()
	{
		return $this->logic;
	}

	function toExpression(IExpressionSubjectConverter $converter)
	{
		return new self(
			$converter->convert($this->subject, $this),
			$this->logic
		);
	}

	function toDalExpression()
	{
		return new UnaryPostfixDalExpression($this);
	}
}

?>