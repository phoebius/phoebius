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
 * Represents a chain order by expressions
 *
 * @ingroup Dal_DB_Sql
 */
class OrderChain extends TypedValueArray implements ISubjective, ISqlCastable
{
	function __construct(array $values = array())
	{
		parent::__construct('OrderBy', $values);
	}

	/**
	 * Drops the ascending logic in all order expressions and sets the ASC logic to the last one
	 * expression
	 * @return OrderChain an object itself
	 */
	function asc()
	{
		if (!$this->isEmpty()) {
			foreach ($this->getList() as $expression) {
				$expression->setNone();
			}

			$this->getLast()->setAsc();
		}

		return $this;
	}

	/**
	 * Drops the ascending logic in all order expressions and sets the DESC logic to the last one
	 * expression
	 * @return OrderChain
	 */
	function desc()
	{
		if (!$this->isEmpty()) {
			foreach ($this->toArray() as $expression) {
				$expression->setNone();
			}

			$this->getLast()->setDesc();
		}

		return $this;
	}

	function toSubjected(ISubjectivity $object)
	{
		$me = new self;

		foreach ($this as $elt) {
			$me->append($elt->toSubjected($object));
		}

		return $me;
	}

	function toDialectString(IDialect $dialect)
	{
		if (!$this->isEmpty()) {
			return
				'ORDER BY '
				. SqlValueExpressionArray::create($this->toArray())
					->toDialectString($dialect);
		}
	}
}

?>