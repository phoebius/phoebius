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
 * Represents column of the database table.
 *
 * @ingroup Dal_DB_Sql
 */
class SqlColumn implements ISqlValueExpression
{
	/**
	 * @var string
	 */
	private $field;

	/**
	 * @var string
	 */
	private $table;

	/**
	 * @param string $field name of the column
	 * @param string $table optional name of the table that owns the column
	 */
	function __construct($field, $table = null)
	{
		Assert::isScalar($field);
		Assert::isScalarOrNull($table);

		$this->field = $field;
		$this->table = $table;
	}

	/**
	 * Gets the string representing a field name
	 * @return string
	 */
	function getFieldName()
	{
		return $this->field;
	}

	/**
	 * Gets the string representing a table name
	 * @return string|null
	 */
	function getTableName()
	{
		return $this->table;
	}

	function toDialectString(IDialect $dialect)
	{
		$sqlSlices = array();

		if ($this->table) {
			$sqlSlices[] = $dialect->quoteIdentifier($this->table);
		}

		$sqlSlices[] = $dialect->quoteIdentifier($this->field);

		$identifier = join('.', $sqlSlices);
		return $identifier;
	}
}

?>