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
 * Represents a database column.
 *
 * A database column have name, type, and default value.
 *
 * Aggregated by:
 * - DBTable
 * - DBConstraint
 * - DBIndex (currently unimplemented)
 *
 * @ingroup Dal_DB_Schema
 */
class DBColumn implements ISqlCastable
{
	/**
	 * @var ISqlValueExpression
	 */
	private $defaultValue;

	/**
	 * @var string
	 */
	private $name;

	/**
	 * @var ISqlType
	 */
	private $type;

	/**
	 * @var DBTable
	 */
	private $table;

	/**
	 * @param string $name name of the column
	 * @param ISqlType $type SQL type of the column
	 */
	function __construct($name, DBTable $table, ISqlType $type)
	{
		Assert::isScalar($name);

		$this->name = $name;
		$this->table = $table;
		$this->type = $type;
	}

	/**
	 * Gets the name of the column
	 *
	 * @return string
	 */
	function getName()
	{
		return $this->name;
	}

	/**
	 * Gets the type of the column
	 *
	 * @return ISqlType
	 */
	function getType()
	{
		return $this->type;
	}

	/**
	 * Gets the table
	 *
	 * @return DBTable
	 */
	function getTable()
	{
		return $this->table;
	}

	/**
	 * Gets the default value of the column, if set
	 *
	 * @return ISqlValueExpression|null
	 */
	function getDefaultValue()
	{
		return $this->defaultValue;
	}

	/**
	 * Sets the default value of the column.
	 *
	 * The default value should be presented as ISqlValueExpression which can be computed as
	 * SQL code
	 *
	 * @param ISqlValueExpression $defaultValue value expression to use as default value
	 * @return ISqlValueExpression itself
	 */
	function setDefaultValue(ISqlValueExpression $defaultValue = null)
	{
		$this->defaultValue = $defaultValue;

		return $this;
	}

	function toDialectString(IDialect $dialect)
	{
		$queryParts[] = $dialect->quoteIdentifier($this->name);
		$queryParts[] = ' ';
		$queryParts[] = $this->type->toDialectString($dialect);

		if ($this->defaultValue) {
			$queryParts[] = ' DEFAULT ';
			$queryParts[] = $this->defaultValue->toDialectString($dialect);
		}

		$string = join('', $queryParts);

		return $string;
	}
}

?>