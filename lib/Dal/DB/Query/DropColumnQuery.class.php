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
 * Represents a query for altering tables for dropping columns
 *
 * @ingroup Dal_DB_Query
 */
final class DropColumnQuery implements ISqlQuery
{
	/**
	 * @var DBColumn
	 */
	private $column;

	function __construct(DBColumn $column)
	{
		$this->column = $column;
	}

	function toDialectString(IDialect $dialect)
	{
		return
			'ALTER TABLE ' . $dialect->quoteIdentifier($this->column->getTable()->getName())
			. ' DROP COLUMN ' . $this->column->toDialectString($dialect)
			. ' CASCADE;';
	}

	function getPlaceholderValues(IDialect $dialect)
	{
		return array();
	}
}

?>