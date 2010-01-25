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
 * Represents an range expression
 *
 * SQL example:
 * @code
 * // "price" BETWEEN 50 AND 100
 * Expression::between("price", 50, 100);
 * @endcode
 * @ingroup Dal_Expression
 */
class BetweenRangeExpression implements ISubjective, IExpression
{
	/**
	 * @var mixed
	 */
	private $subject;

	/**
	 * @var mixed
	 */
	private $from;

	/**
	 * @var mixed
	 */
	private $to;

	/**
	 * @param mixed $subject logical subject
	 * @param mixed $from starting value of the range
	 * @param mixed $to ending value of the range
	 */
	function __construct($subject, $from, $to)
	{
		$this->subject = $subject;
		$this->from = $from;
		$this->to = $to;
	}

	function toSubjected(ISubjectivity $object)
	{
		return new self (
			$object->convert($this->subject, $this),
			$object->convert($this->from, $this),
			$object->convert($this->to, $this)
		);
	}

	function toDialectString(IDialect $dialect)
	{
		$compiledSlices = array();

		$compiledSlices[] = $this->subject->toDialectString($dialect);
		$compiledSlices[] = 'BETWEEN';
		$compiledSlices[] = '(';
		$compiledSlices[] = $this->from->toDialectString($dialect);
		$compiledSlices[] = ')';
		$compiledSlices[] = 'AND';
		$compiledSlices[] = '(';
		$compiledSlices[] =  $this->to->toDialectString($dialect);
		$compiledSlices[] = ')';

		$compiledString = join(' ', $compiledSlices);

		return $compiledString;
	}
}

?>