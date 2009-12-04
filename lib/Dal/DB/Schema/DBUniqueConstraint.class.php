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
 * Represents a UNIQUE constaint. It is a database constraint that ensures that the data contained
 * in a field or a group of fields is unique with respect to all the rows in the table
 *
 * @ingroup Dal_DB_Schema
 */
class DBUniqueConstraint extends DBConstraint
{
	/**
	 * @var SqlFieldArray
	 */
	private $fields;

	/**
	 * @param array $fields array of field names affected by the constaint
	 */
	function __construct(array $fields)
	{
		$this->fields = new SqlFieldArray($fields);
	}

	/**
	 * Get the fields affected by the constraint
	 *
	 * @return array of string
	 */
	function getFields()
	{
		return $this->fields->toArray();
	}

	function getIndexableFields()
	{
		return array();
	}

	function toDialectString(IDialect $dialect)
	{
		return $this->getHead($dialect) . ' (' . $this->fields->toDialectString($dialect) . ')';
	}

	protected function getHead(IDialect $dialect)
	{
		return 'UNIQUE';
	}
}

?>