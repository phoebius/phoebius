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
 * Represents an range expression
 *
 * SQL example:
 * @code
 * // "price" BETWEEN 50 AND 100
 * Expression::between("price", 50, 100);
 * @endcode
 * @ingroup Core_Expression
 */
class BetweenRangeExpression implements IExpression
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

	function __construct($subject, $from, $to)
	{
		$this->subject = $subject;
		$this->from = $from;
		$this->to = $to;
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
	function getFrom()
	{
		return $this->from;
	}

	/**
	 * @return mixed
	 */
	function getTo()
	{
		return $this->to;
	}

	function toExpression(IExpressionSubjectConverter $converter)
	{
		return new self(
			$converter->convert($this->subject, $this),
			$converter->convert($this->from, $this),
			$converter->convert($this->to, $this)
		);
	}

	function toDalExpression()
	{
		return new BetweenRangeDalExpression($this);
	}
}

?>