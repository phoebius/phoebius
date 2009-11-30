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
 * @ingroup Dal_Expression
 */
class UnaryPostfixExpression implements ISubjective, IExpression
{
	/**
	 * @var mixed
	 */
	private $subject;

	/**
	 * @var UnaryPostfixLogicalOperator
	 */
	private $operator;

	/**
	 * @param mixed $subject logical subject
	 * @param UnaryPostfixLogicalOperator $operator logical operator
	 */
	function __construct($subject, UnaryPostfixLogicalOperator $operator)
	{
		$this->subject = $subject;
		$this->operator = $operator;
	}

	function toSubjected(ISubjectivity $object)
	{
		return new self (
			$object->subject($this->subject, $this),
			$this->logic
		);
	}

	function toDialectString(IDialect $dialect)
	{
		$compiledSlices = array();

		$compiledSlices[] = '(';
		$compiledSlices[] = $this->subject->toDialectString($dialect);
		$compiledSlices[] = ')';
		$compiledSlices[] = $this->operator->toDialectString($dialect);

		$compiledString = join(' ', $compiledSlices);

		return $compiledString;
	}
}

?>