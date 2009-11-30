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
 * Represents a chain of expressions used in ordering the resulting database rows
 *
 * @ingroup Dal_DB_Sql
 */
class OrderChain extends TypedValueArray implements ISubjective, ISqlCastable
{
	/**
	 * @param array $array initial OrderBy objects to be added to the value list
	 */
	function __construct(array $values = array())
	{
		parent::__construct('OrderBy', $values);
	}

	/**
	 * Sets the ascending logic to all expressions added to the OrderChain
	 * @return OrderChain itself
	 */
	function setAsc()
	{
		foreach ($this->getList() as $expression) {
			$expression->setAsc();
		}

		return $this;
	}

	/**
	 * Sets the descending logic to all expressions added to the OrderChain
	 * @return OrderChain
	 */
	function setDesc()
	{
		foreach ($this->toArray() as $expression) {
			$expression->setDesc();
		}

		return $this;
	}

	function toSubjected(ISubjectivity $object)
	{
		$me = new self;

		foreach ($this->toArray() as $elt) {
			$me->append($elt->toSubjected($object));
		}

		return $me;
	}

	function toDialectString(IDialect $dialect)
	{
		if (!$this->isEmpty()) {
			$list = new SqlValueExpressionArray($this->toArray());

			return 'ORDER BY ' . $list->toDialectString($dialect);
		}
	}
}

?>