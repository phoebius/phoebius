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
 * Represents a database schema that aggregates DBTable objects
 *
 * @ingroup Dal_DB_Schema
 */
final class DBSchema implements ISqlCastable
{
	/**
	 * @var array of DBTable
	 */
	private $tables = array();

	/**
	 * Adds the DBTable object to the schema
	 *
	 * @param DBTable $table table to add
	 * @throws DuplicationException thrown when another DBTable with the same name already added
	 * @return DBSchema itself
	 */
	function addTable(DBTable $table)
	{
		$name = $table->getName();

		if (isset($this->tables[$name])) {
			throw new DuplicationException('table', $name);
		}

		$this->tables[$name] = $table;

		return $this;
	}

	/**
	 * Adds the DBTable objects to the schema
	 *
	 * @param array $table set of table to be added
	 * @throws DuplicationException thrown when another DBTable with the same name already added
	 * @return DBSchema itself
	 */
	function addTables(array $tables)
	{
		foreach ($tables as $table) {
			$this->addTable($table);
		}

		return $this;
	}

	/**
	 * Gets the DBTable object by its name
	 *
	 * @param string $name name of the table to look up
	 * @throws ArgumentException thrown when no DBTable object identified by name found
	 * @return DBTable
	 */
	function getTable($name)
	{
		Assert::isScalar($name);

		if (!isset($this->tables[$name])) {
			throw new ArgumentException('name', 'not found');
		}

		return $this->tables[$name];
	}

	/**
	 * Gets the list of DBTable objects added to DBSchema
	 *
	 * @return array of DBTable
	 */
	function getTables()
	{
		return $this->tables;
	}

	/**
	 * Creates a list of ISqlQuery objects that represent a DDL for the list of DBTable objects
	 * added to schema
	 *
	 * @return SqlQuerySet
	 */
	function toQueries()
	{
		$DDLs = new SqlQuerySet;
		$constraintDDLs = new SqlQuerySet;
		$indexDDLs = new SqlQuerySet;
		
		foreach ($this->tables as $table) {
			$DDLs->addQuery($table->getQuery());
			
			$constraintDDLs->addQueries($table->getConstraintQueries());
			
			$indexDDLs->addQueries($table->getIndexQueries());
		}

		return 
			$DDLs
				->merge($constraintDDLs)
				->merge($indexDDLs);
	}

	function toDialectString(IDialect $dialect)
	{
		return $this->toQueries()->toDialectString($dialect);
	}
}

?>