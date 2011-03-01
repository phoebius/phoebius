<?php
/* ***********************************************************************************************
 *
 * Phoebius Framework
 *
 * **********************************************************************************************
 *
 * Copyright (c) 2011 Scand Ltd.
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
 * Represents a database diff
 *
 * @ingroup Dal_DB_Schema
 */
final class DBDiff implements ISqlCastable
{
	private $createTables = array();
	private $dropTables = array();
	
	private $createColumns = array();
	private $dropColumns = array();
	
	private $createConstraints = array();
	private $dropConstraints = array();
	
	private $createIndexes = array();
	private $dropIndexes = array();
	
	/**
	 * Creates a diff between two scemas
	 * @param DBSchema $from
	 * @param DBSchema $to
	 * 
	 * @return DBDiff itself
	 */
	function make(DBSchema $from, DBSchema $to)
	{
		// Firstly, process tables
		// Then, for each table process columns, constraints, indexes
		
		$fromTables = $from->getTables();
		$toTables   = $to->getTables();
		
		$this->compare(&$this->createTables, &$this->dropTables, $fromTables, $toTables);
		
		$sameTables = array_intersect_key(array_keys($fromTables), array_keys($toTables));

		foreach ($sameTables as $name) {
			$fromTable = $from->getTable($name);
			$toTable   = $to->getTable($name);
			
			$this->compare(&$this->createColumns, &$this->dropColumns, $fromTable->getColumns(), $toTable->getColumns());
			$this->compare(&$this->createConstraints, &$this->dropConstraints, $fromTable->getConstraints(), $toTable->getConstraints());
			$this->compare(&$this->createIndexes, &$this->dropIndexes, $fromTable->getIndexes(), $toTable->getIndexes());
		}
		
		return $this;
	}
	
	/**
	 * Applies a diff to the specified schema
	 * @param DBSchema $schema
	 * @return DBDiff itself
	 */
	function apply(DBSchema $schema)
	{
		foreach ($this->createTables as $table) {
			$schema->addTable($table);
		}
		
		foreach ($this->createColumns as $column) {
			$schema->getTable($tableName)->addColumn($column);
		}
		
		foreach ($this->createConstraints as $constaint) {
			$schema->getTable($tableName)->addConstraint($constaint);
		}
		
		foreach ($this->createIndexes as $index) {
			$schema->getTable($tableName)->addIndex($index);
		}
		
		return $this;
	}
	
	/**
	 * Clears the diff
	 * @return DBDiff
	 */
	function clear()
	{
		$this->createColumns = $this->createConstraints = 
			$this->createIndexes = $this->createTables =
			$this->dropColumns = $this->dropConstraints = 
			$this->dropIndexes = $this->dropTables = array();
			
		return $this;
	}
	
	/**
	 * Reverses the diff direction
	 * @return DBDiff itself
	 */
	function swap()
	{
		list ($this->createTables, $this->dropTables) = array ($this->dropTables, $this->createTables);
		list ($this->createColumns, $this->dropColumns) = array ($this->dropColumns, $this->createColumns);
		list ($this->createConstraints, $this->dropConstraints) = array ($this->dropConstraints, $this->createConstraints);
		list ($this->createIndexes, $this->dropIndexes) = array ($this->dropIndexes, $this->createIndexes);
		
		return $this;
	}
	
	function toDialectString(IDialect $dialect)
	{
		// tables
		// columns
		// constraints
		// indexes
		
		$yield = array();

		foreach ($this->dropIndexes as $index) {
			$yield[] = new DropIndexQuery($index);
		}

		foreach ($this->dropConstraints as $constraint) {
			$yield[] = new DropConstraintQuery($constraint);
		}

		foreach ($this->dropColumns as $column) {
			$yield[] = new DropColumnQuery($column);
		}
		
		foreach ($this->dropTables as $table) {
			$yield[] = new DropTableQuery($table);
		}
		
		
		foreach ($this->createTables as $table) {
			$yield[] = new CreateTableQuery($table);
		}
		
		foreach ($this->createColumns as $column) {
			$yield[] = new CreateColumnQuery($column);
		}
		
		foreach ($this->createConstraints as $constraint) {
			$yield[] = new CreateConstraintQuery($constraint);
			
			foreach ($this->createTables as $table) {
				foreach ($table->getConstraintQueries() as $query) {
					$yield[] = $query;
				}
			}
		}
		
		foreach ($this->createIndexes as $index) {
			$yield[] = new CreateIndexQuery($index);
			
			foreach ($this->createTables as $table) {
				foreach ($table->getIndexQueries() as $query) {
					$yield[] = $query;
				}
			}
		}
			
		foreach ($this->createTables as $table) {
			foreach ($dialect->getExtraTableQueries($table) as $query) {
				$yield[] = $query;
			}
		}
		
		$set = new SqlQuerySet($yield);
		return $set->toDialectString($dialect);
	}
	
	private function compare(&$new, &$drop, array $from, array $to)
	{
		// track dropped: array_diff_key(from, to)
		// track added:   array_diff_key(to, from)
		
		$dropped = array_diff_key($from, $to);
		foreach ($dropped as $item) {
			$drop[] = $item;
		}
		
		$added = array_diff_key($to, $from);
		foreach ($added as $item) {
			$new[] = $item;
		}
	}
}
