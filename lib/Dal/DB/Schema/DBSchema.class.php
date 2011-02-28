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
	 * Drops the table from the schema
	 * @param string $name
	 * 
	 * @return DBSchema an object itself
	 */
	function dropTable($name) 
	{
		Assert::isScalar($name);

		if (!isset($this->tables[$name])) {
			throw new ArgumentException('name', 'not found');
		}
		
		unset ($this->tables[$name]);

		return $this;
	}

	/**
	 * Creates a list of ISqlQuery objects that represent a DDL for the list of DBTable objects
	 * added to schema
	 *
	 * @return SqlQuerySet
	 */
	function toQueries(IDialect $dialect = null)
	{
		$DDLs = new SqlQuerySet;
		$constraintDDLs = new SqlQuerySet;
		$indexDDLs = new SqlQuerySet;
		$extraDDLs = new SqlQuerySet;
		
		foreach ($this->tables as $table) {
			$DDLs->addQueries($table->getQueries());
			
			$constraintDDLs->addQueries($table->getConstraintQueries());
			
			$indexDDLs->addQueries($table->getIndexQueries());
			
			if ($dialect) {
				$extraDDLs->addQueries($dialect->getExtraTableQueries($table));
			}
		}
 
		$DDLs
			->merge($constraintDDLs)
			->merge($indexDDLs)
			->merge($extraDDLs);

		return $DDLs;
	}

	function toDialectString(IDialect $dialect)
	{
		return $this->toQueries($dialect)->toDialectString($dialect);
	}
}

?>