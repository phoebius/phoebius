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
 * @ingroup Dal_DB_Schema
 */
final class DBSchema implements ISqlCastable
{
	/**
	 * @var array of {@link DBTable}
	 */
	private $tables = array();

	/**
	 * @return DBSchema
	 */
	static function create()
	{
		return new self;
	}

	/**
	 * @return DBSchema
	 */
	function setTables(array $tables)
	{
		foreach ($tables as $table) {
			$this->setTable($table);
		}

		return $this;
	}

	/**
	 * @return DBSchema
	 */
	function dropTables()
	{
		$this->tables = array();

		return $this;
	}

	/**
	 * @return DBSchema
	 */
	function addTables(array $tables)
	{
		foreach ($tables as $table) {
			$this->addTable($table);
		}

		return $this;
	}

	/**
	 * @return DBSchema
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
	 * @return DBSchema
	 */
	function setTable(DBTable $table)
	{
		$this->tables[$table->getName()] = $table;

		return $this;
	}

	/**
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
	 * @return array of {@link DBTable}
	 */
	function getTables()
	{
		return $this->tables;
	}

	/**
	 * @return array of {@link ISqlQuery}
	 */
	function toQueries(IDialect $dialect)
	{
		$queries = array();
		foreach ($this->tables as $table) {
			$queries += $table->toQueries($dialect);
		}

		return $queries;
	}

	/**
	 * Casts an object to the SQL dialect string
	 * @return string
	 */
	function toDialectString(IDialect $dialect)
	{
		$sql = array();

		foreach ($this->tables as $table) {
			foreach ($table->toQueries($dialect) as $query) {
				$sql[] = $query->toDialectString($dialect);
			}
		}

		return join(StringUtils::DELIM_STANDART.StringUtils::DELIM_STANDART, $sql);
	}
}

?>