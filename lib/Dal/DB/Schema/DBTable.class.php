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
 * Represents a database table
 *
 * @ingroup Dal_DB_Schema
 */
class DBTable
{
	/**
	 * @var string
	 */
	private $name;
	
	/**
	 * @var DBPrimaryKeyConstraint
	 */
	private $pk;

	/**
	 * @var array of DBColumn
	 */
	private $columns = array();

	/**
	 * @var array of DBConstraint
	 */
	private $constraints = array();
	
	/**
	 * @var array of DBIndex
	 */
	private $indexes = array();

	/**
	 * @param string $name name of the table
	 */
	function __construct($name)
	{
		Assert::isScalar($name);

		$this->name = $name;
	}

	function __sleep()
	{
		return array (
			'name', 'columns', 'constraints', 'indexes'
		);
	}
	
	function __wakeup()
	{
		foreach ($this->constraints as $constraint) {
			if ($constraint instanceof DBPrimaryKeyConstraint) {
				$this->pk = $constraint;
			}
		}
	}

	/**
	 * Gets the name of the table
	 *
	 * @return string
	 */
	function getName()
	{
		return $this->name;
	}

	/**
	 * Adds a named DBColumn object to the table
	 *
	 * @param DBColumn $column a column to add to the table
	 * @throws DuplicationException thrown when another column with the same name already added
	 * @return DBTable itself
	 */
	function addColumn(DBColumn $column)
	{
		$name = $column->getName();

		if (isset($this->columns[$name])) {
			throw new DuplicationException('column', $name);
		}

		$this->columns[$name] = $column;

		return $this;
	}

	/**
	 * Gets a named DBColumn object to the table
	 *
	 * @param string $name
	 * @throws ArgumentException thrown when no DBColumn object identified by name found
	 * @return DBColumn
	 */
	function getColumn($name)
	{
		Assert::isScalar($name);

		if (!isset($this->columns[$name])) {
			throw new ArgumentException('name', 'not found');
		}

		return $this->columns[$name];
	}

	/**
	 * Drops a named DBColumn object to the table
	 *
	 * @param string $name
	 * @throws ArgumentException thrown when no DBColumn object identified by name found
	 * @return DBTable itself
	 */
	function dropColumn($name)
	{
		Assert::isScalar($name);

		if (!isset($this->columns[$name])) {
			throw new ArgumentException('name', 'not found');
		}
		
		unset ($this->columns[$name]);

		return $this;
	}

	/**
	 * Gets the DBColumn objects added to the table
	 *
	 * @return array of DBColumn
	 */
	function getColumns()
	{
		return $this->columns;
	}

	/**
	 * Gets the list of column names representing the table
	 *
	 * @return array of string
	 */
	function getFields()
	{
		return array_keys($this->columns);
	}

	/**
	 * Adds a table constraint
	 *
	 * @param DBConstraint $constraint constraint to add
	 * @throws DuplicationException thrown when another constaint with the same name already added
	 * @return DBTable itself
	 */
	function addConstraint(DBConstraint $constraint)
	{
		$name = $constraint->getName();

		if ($name) {
			if (isset($this->constraints[$name])) {
				throw new DuplicationException('constraint', $name);
			}
		}
		else {
			$name = 
				'constraint_' .
				join('_', $constraint->getFields()) 
				. (sizeof($this->constraints) + 1);
			$constraint->setName($name);
		}

		$this->constraints[$name] = $constraint;
		
		if ($constraint instanceof DBPrimaryKeyConstraint) {
			if ($this->pk) {
				throw new DuplicationException('constraint', $name);
			}
			
			$this->pk = $constraint;
		}

		return $this;
	}

	/**
	 * Drops a named DBConstraint object to the table
	 *
	 * @param string $name
	 * @throws ArgumentException thrown when no DBConstraint object identified by name found
	 * @return DBTable itself
	 */
	function dropConstraint($name)
	{
		Assert::isScalar($name);

		if (!isset($this->constraints[$name])) {
			throw new ArgumentException('name', 'not found');
		}
		
		if ($this->constraints[$name] === $this->pk) {
			$this->pk = null;
		}
		
		unset ($this->constraints[$name]);

		return $this;
	}

	/**
	 * Gets the DBConstraint objects added to the table
	 *
	 * @return array of DBConstraint
	 */
	function getConstraints()
	{
		return $this->constraints;
	}

	/**
	 * Adds a table index
	 *
	 * @param DBIndex $index index to add
	 * @throws DuplicationException thrown when another index with the same name already added
	 * @return DBTable itself
	 */
	function addIndex(DBIndex $index)
	{
		$name = $index->getName();

		if ($name) {
			if (isset($this->indexes[$name])) {
				throw new DuplicationException('index', $name);
			}
		}
		else {
			$name = 
				'index_' .
				join('_', $index->getFields()) 
				. (sizeof($this->indexes) + 1);
			$index->setName($name);
		}

		$this->indexes[$name] = $index;

		return $this;
	}

	/**
	 * Drops a named DBIndex object to the table
	 *
	 * @param string $name
	 * @throws ArgumentException thrown when no DBIndex object identified by name found
	 * @return DBTable itself
	 */
	function dropIndex($name)
	{
		Assert::isScalar($name);

		if (!isset($this->indexes[$name])) {
			throw new ArgumentException('name', 'not found');
		}
		
		unset ($this->indexes[$name]);

		return $this;
	}

	/**
	 * Gets the DBIndex objects added to the table
	 *
	 * @return array of DBIndex
	 */
	function getIndexes()
	{
		return $this->indexes;
	}
	
	function getQueries()
	{
		$yield = array(
			new CreateTableQuery($this, true),
		);
		
		if ($this->pk) {
			$yield[] = new CreateConstraintQuery($this, $this->pk);
		}
		
		return $yield;
	}
	
	function getConstraintQueries()
	{
		$queries = array();
		
		foreach ($this->constraints as $constraint) {
			if ($constraint !== $this->pk) {
				$queries[] = new CreateConstraintQuery($this, $constraint);
			}
		}
		
		return $queries;
	}
	
	function getIndexQueries()
	{
		$queries = array();
		
		foreach ($this->indexes as $index) {
			$queries[] = new CreateIndexQuery($this, $index);
		}
		
		return $queries;
	}
}

?>