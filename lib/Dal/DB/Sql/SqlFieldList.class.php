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
 * Represents a list of fields, that can be casted to SQL value set
 * @ingroup Dal_DB_Sql
 */
class SqlFieldList extends TypedValueList implements ISqlCastable
{
	/**
	 * Creates an instance of {@link SqlFieldList}
	 * @param array $initialFields initial scalar names of the fields to be imported
	 * @return SqlFieldList
	 */
	static function create(array $initialFields = array())
	{
		return new self($initialFields);
	}

	/**
	 * Append a scalar name of the field to the list
	 * @param scalar $field
	 * @return SqlFieldList an object itself
	 */
	function add($fieldName)
	{
		$this->addTransparent($fieldName);

		return $this;
	}

	/**
	 * Casts an object to the SQL dialect string
	 * @return string
	 */
	function toDialectString(IDialect $dialect)
	{
		$quotedFields = array();
		foreach ($this->getList() as $field) {
			$quotedFields[] = $dialect->quoteIdentifier($field);
		}

		$joinedFields = join(', ', $quotedFields);

		return $joinedFields;
	}

	/**
	 * Determines whether the specified value is of valid type supported by the list implementation
	 * @return boolean
	 */
	protected function isValueOfValidType($value)
	{
		return is_scalar($value);
	}
}

?>