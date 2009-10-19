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
 * Aggregated by:
 *  - DBTable
 * @ingroup Dal_DB_Schema
 */
abstract class DBConstraint implements ISqlCastable
{
	/**
	 * @var string
	 */
	private $name;

	/**
	 * Returns the affected columns, if any
	 * @return array of {@link DBColumn}
	 */
	abstract function getIndexedColumns();

	/**
	 * @return string
	 */
	function getName()
	{
		return $this->name;
	}

	/**
	 * @return DBColumn
	 */
	function setName($name)
	{
		Assert::isScalar($name);

		$this->name = $name;

		return $this;
	}

	/**
	 * Casts an object to the SQL dialect string
	 * @return string
	 */
	function toDialectString(IDialect $dialect)
	{
		return $this->getHead($dialect) . ' ' . $this->getSql();
	}

	/**
	 * @return string
	 */
	protected function getHead(IDialect $dialect)
	{
		return 'CONSTRAINT '.$dialect->quoteIdentifier($this->name);
	}
}

?>