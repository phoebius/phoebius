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
	 * @var array of DBColumn
	 */
	private $columns = array();

	/**
	 * @var array of DBConstraint
	 */
	private $constraints = array();
	
	/**
	 * @var DBPrimaryKeyConstraint
	 */
	private $pk;
	
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
			'name', 'columns', 'constraints'
		);
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
	 * Adds a named DBColumn objects to the table
	 *
	 * @param array $columns a list of DBColumn objects to add to the table
	 * @throws DuplicationException thrown when another column with the same name already added
	 * @return DBTable itself
	 */
	function addColumns(array $columns)
	{
		foreach ($columns as $column) {
			$this->addColumn($column);
		}

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
	 * Gets the named DBColumn object
	 *
	 * @param string $name name of the column to look up
	 * @return DBColumn
	 */
	function getColumn($name)
	{
		Assert::isScalar($name);

		if (!isset($this->columns[$name])) {
			throw new ArgumentException('name', 'column not found');
		}

		return $this->columns[$name];
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