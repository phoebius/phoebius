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
 * Represents a query for altering tables for creating constraints
 *
 * @ingroup Dal_DB_Query
 */
final class CreateConstraintQuery implements ISqlQuery
{
	/**
	 * @var DBTable
	 */
	private $table;

	/**
	 * @var DBConstraint
	 */
	private $constraint;

	/**
	 * @param DBTable $table a table object that represent an expected database table
	 */
	function __construct(DBTable $table, DBConstraint $constraint)
	{
		$this->table = $table;
		$this->constraint = $constraint;
	}

	function toDialectString(IDialect $dialect)
	{
		return
			'ALTER TABLE ' . $dialect->quoteIdentifier($this->table->getName())
			. ' ADD ' . $this->constraint->toDialectString($dialect)
			. ';';
	}

	function getPlaceholderValues(IDialect $dialect)
	{
		return array();
	}
}

?>