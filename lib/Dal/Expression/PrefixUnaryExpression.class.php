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
 * Represents a unary prefix expression.
 *
 * @ingroup Dal_Expression
 */
class PrefixUnaryExpression implements ISubjective, IExpression
{
	/**
	 * @var mixed
	 */
	private $subject;

	/**
	 * @var PrefixUnaryLogicalOperator
	 */
	private $operator;

	/**
	 * @param PrefixUnaryLogicalOperator $operator logical operator
	 * @param mixed $subject logical subject
	 */
	function __construct(PrefixUnaryLogicalOperator $operator, $subject)
	{
		$this->operator = $operator;
		$this->subject = $subject;
	}

	function toSubjected(ISubjectivity $object)
	{
		return new self(
			$this->operator,
			$object->subject($this->subject, $this)
		);
	}

	function toDialectString(IDialect $dialect)
	{
		$compiledSlices = array();

		$compiledSlices[] = $this->operator->toDialectString($dialect);
		$compiledSlices[] = '(';
		$compiledSlices[] = $this->subject->toDialectString($dialect);
		$compiledSlices[] = ')';

		$compiledString = join(' ', $compiledSlices);

		return $compiledString;
	}
}

?>