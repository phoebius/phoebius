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
 * Represents the IN expression used in query logic
 *
 * SQL example:
 * @code
 * // "type" IN ("completed", "pending")
 * Expression::in("type", array("completed", "pending"));
 * @endcode
 *
 * @ingroup Core_Expression
 */
class InSetExpression implements ISubjective
{
	/**
	 * @var mixed
	 */
	private $subject;

	/**
	 * @var array
	 */
	private $set;

	/**
	 * @var InSetLogicalOperator
	 */
	private $logic;

	/**
	 * @param mixed expression subject
	 * @param array set of values the subject should match
	 * @param InSetLogicalOperator|null matching operator
	 */
	function __construct($subject, array $set, InSetLogicalOperator $logic = null)
	{
		$this->subject = $subject;
		$this->set = $set;
		$this->logic =
			$logic
				? $logic
				: InSetLogicalOperator::in();
	}

	/**
	 * @return mixed
	 */
	function getSubject()
	{
		return $this->subject;
	}

	/**
	 * @return array
	 */
	function getSet()
	{
		return $this->set;
	}

	/**
	 * @return InSetLogicalOperator
	 */
	function getLogicalOperator()
	{
		return $this->logic;
	}

	function toSubjected(ISubjectivity $object)
	{
		return new self (
			$object->subject($this->subject, $this),
			$this->convertSet($object),
			$this->logic
		);
	}

	/**
	 * @return array
	 */
	private function convertSet(ISubjectivity $object)
	{
		$set = array();
		foreach ($this->set as $item) {
			$set[] = $object->subject($item);
		}

		return $set;
	}

	function toDialectString(IDialect $dialect)
	{
		$values = new SqlValueExpressionArray($this->set);

		$compiledSlices = array();

		$compiledSlices[] = '(';
		$compiledSlices[] = $this->subject->toDialectString($dialect);
		$compiledSlices[] = ')';
		$compiledSlices[] = $this->logic->toDialectString($dialect);
		$compiledSlices[] = '(';
		$compiledSlices[] = $values->toDialectString($dialect);
		$compiledSlices[] = ')';

		$compiledString = join(' ', $compiledSlices);

		return $compiledString;
	}
}

?>