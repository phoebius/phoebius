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
 * Represents an database index.
 *
 * Index can be optinally named.
 *
 * @ingroup Dal_DB_Schema
 */
class DBIndex implements ISqlCastable
{
	/**
	 * @var string
	 */
	private $name;
	
	/**
	 * @var DBTable
	 */
	private $table;
	
	/**
	 * @var array
	 */
	private $fields;
	
	/**
	 * @param array of string $fields
	 */
	function __construct($name, DBTable $table, array $fields)
	{
		Assert::isScalar($name);
		Assert::isNotEmpty($fields, 'index cannot be across zero fields');
	
		$this->name = $name;
		$this->table = $table;
		$this->fields = $fields;
	}
	
	/**
	 * Gets the list of columns to be indexed
	 */
	function getFields()
	{
		return $this->fields;
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
	 * Gets the name of the index
	 *
	 * @return string
	 */
	function getName()
	{
		return $this->name;
	}
	
	function toDialectString(IDialect $dialect)
	{
		return 
			'INDEX ' 
			. $dialect->quoteIdentifier($this->name)
			. ' ON ' . $dialect->quoteIdentifier($this->table->getName())
			. ' (' . $this->getFieldsAsString($dialect) . ')';
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