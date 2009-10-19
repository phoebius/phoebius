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
 * Represents a list of values, that can be casted to SQL value set
 * @ingroup Dal_DB_Sql
 */
class SqlValueList extends TypedValueList implements ISqlValueExpression
{
	/**
	 * Creates an instance of {@link SqlValueList}
	 * @param array $initialValues list of initial {@link SqlValue} values to be imported
	 * @return SqlFieldList
	 */
	static function create(array $initialValues = array())
	{
		return new self($initialValues);
	}

	/**
	 * Append a {@link SqlValue} value to the list
	 * @return SqlValueList an object itself
	 */
	function add(SqlValue $value)
	{
		$this->append($value);

		return $this;
	}

	/**
	 * Casts an object to the SQL dialect string
	 * @return string
	 */
	function toDialectString(IDialect $dialect)
	{
		$quotedValues = array();
		foreach ($this->getList() as $value) {
			$quotedValues[] = $value->toDialectString($dialect);
		}

		$joinedValues = join(', ', $quotedValues);

		return $joinedValues;
	}

	/**
	 * Determines whether the specified value is of valid type supported by the list implementation
	 * @return boolean
	 */
	protected function isValueOfValidType($value)
	{
		return ($value instanceof SqlValue);
	}
}

?>