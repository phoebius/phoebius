<?php
/* ***********************************************************************************************
 *
 * Phoebius Framework
 *
 * **********************************************************************************************
 *
 * Copyright (c) 2009 Scand Ltd.
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
 * @ingroup Dal_Expression
 */
class BinaryExpression implements ISubjective, IExpression
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
	private $operator;

	/**
	 * @param mixed $subject logical subject
	 * @param BinaryLogicalOperator $operator logical operator
	 * @param mixed $value value to match the subject
	 */
	function __construct($subject, BinaryLogicalOperator $operator, $value)
	{
		$this->subject = $subject;
		$this->operator = $operator;
		$this->value = $value;
	}

	function toSubjected(ISubjectivity $object)
	{
		return new self (
			$object->subject($this->subject, $this),
			$this->operator,
			$object->subject($this->value, $this)
		);
	}

	function toDialectString(IDialect $dialect)
	{
		$compiledSlices = array();

		$compiledSlices[] = '(';
		$compiledSlices[] = $this->subject->toDialectString($dialect);
		$compiledSlices[] = ')';
		$compiledSlices[] = $this->operator->toDialectString($dialect);
		$compiledSlices[] = '(';
		$compiledSlices[] = $this->value->toDialectString($dialect);
		$compiledSlices[] = ')';

		$compiledString = join(' ', $compiledSlices);

		return $compiledString;
	}
}

?>