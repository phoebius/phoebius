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
class DBUniqueConstraint extends DBConstraint
{
	/**
	 * @var array of {@link DBColumn}
	 */
	private $columns = array();

	/**
	 * @return DBUniqueConstraint
	 */
	static function create(array $columns = array())
	{
		return new self ($columns);
	}

	function __construct(array $columns = array())
	{
		$this->setColumns($columns);
	}

	/**
	 * @return DBUniqueConstraint
	 */
	function setColumns(array $columns)
	{
		$this->columns = array();
		foreach ($columns as $column) {
			$this->addColumn($column);
		}

		return $this;
	}

	/**
	 * @return DBUniqueConstraint
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
	 * @return DBUniqueConstraint
	 */
	function dropColumns()
	{
		$this->columns = array();

		return $this;
	}

	/**
	 * @return array of {@link DBColumn}
	 */
	function getColumns()
	{
		return $this->columns;
	}

	/**
	 * @return array of {@link DBColumn}
	 */
	function getIndexedColumns()
	{
		return array();
	}

	/**
	 * Casts an object to the SQL dialect string
	 * @return string
	 */
	function toDialectString(IDialect $dialect)
	{
		return $this->getHead($dialect) . ' (' . $this->getFieldList($dialect) . ')';
	}

	/**
	 * @return string
	 */
	protected function getHead(IDialect $dialect)
	{
		return 'UNIQUE';
	}

	private function getFieldList(IDialect $dialect)
	{
		return SqlFieldArray::create(array_keys($this->columns))->toDialectString($dialect);
	}
}

?>