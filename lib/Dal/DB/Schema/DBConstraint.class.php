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
 * Represents an abstract database constraint.
 *
 * @ingroup Dal_DB_Schema
 */
abstract class DBConstraint implements ISqlCastable
{
	/**
	 * @var string
	 */
	private $name;
	
	/**
	 * @var array
	 */
	private $fields;
	
	/**
	 * @var DBTable
	 */
	private $table;
	
	/**
	 * @param array of string $fields
	 */
	function __construct($name, DBTable $table, array $fields)
	{
		Assert::isScalar($name);
		Assert::isNotEmpty($fields, 'constraint cannot be across zero fields');
		
		$this->name = $name;
		$this->table = $table;
		$this->fields = $fields;
	}
	
	function getFields()
	{
		return $this->fields;
	}

	/**
	 * Gets the name of the constraint
	 *
	 * @return string
	 */
	function getName()
	{
		return $this->name;
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
	 * Gets the SQL representation of the constraint's head
	 *
	 * @param IDialect $dialect
	 *
	 * @return string
	 */
	protected function getHead(IDialect $dialect)
	{
		return 'CONSTRAINT ' . $dialect->quoteIdentifier($this->name);
	}
	
	/**
	 * Gets the list of covered fields as string
	 * @param IDialect $dialect
	 * @return string
	 */
	protected function getFieldsAsString(IDialect $dialect)
	{
		$fields = new SqlFieldArray($this->fields);
		
		return $fields->toDialectString($dialect);
	}
}

?>