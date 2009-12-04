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
 * Represents a query that modifies a row
 *
 * @ingroup Dal_DB_Query
 */
abstract class RowModificationQuery
{
	/**
	 * @var string
	 */
	private $table;

	/**
	 * @var SqlRow
	 */
	private $row;

	function __construct()
	{
		$this->row = new SqlRow;
	}

	function __clone()
	{
		$this->row = clone $this->row;
	}

	/**
	 * Sets the field to be changed and the new value to set
	 *
	 * @param string $field field name to update
	 * @param ISqlValueExpression $value value to set
	 *
	 * @return RowModificationQuery itself
	 */
	function set($field, ISqlValueExpression $value)
	{
		$this->row->set($field, $value);

		return $this;
	}

	/**
	 * Sets the fields to be changed and corresponding values to be set
	 *
	 * @param array $values an associative array of fields to be changed and corresponding
	 * 						values to be set
	 *
	 * @return RowModificationQuery itself
	 */
	function setValues(array $values)
	{
		$this->row->append($values);

		return $this;
	}

	/**
	 * Gets the row to be modified
	 *
	 * @return SqlRow
	 */
	function getRow()
	{
		return $this->row;
	}
}

?>