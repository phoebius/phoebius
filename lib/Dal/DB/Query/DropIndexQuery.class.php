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
 * Represents a query for altering tables for dropping indexes
 *
 * @ingroup Dal_DB_Query
 */
final class DropIndexQuery implements ISqlQuery
{
	/**
	 * @var DBIndex
	 */
	private $index;

	/**
	 * @param DBTable $table a table object that represent an expected database table
	 * @param DBIndex $index index to be dropped
	 */
	function __construct(DBIndex $index)
	{
		$this->index = $index;
	}

	function toDialectString(IDialect $dialect)
	{
		//
		// FIXME move to IDialect
		// 
		
		return
			'DROP INDEX ' . $dialect->quoteIdentifier($this->index->getName())
			. ( $dialect->getDBDriver()->is(DBDriver::MYSQL) ? ' ON ' . $dialect->quoteIdentifier($this->index->getTable()->getName()) : '' )
			. ( $dialect->getDBDriver()->is(DBDriver::PGSQL) ? ' CASCADE ' : '' )
			. ';';
	}

	function getPlaceholderValues(IDialect $dialect)
	{
		return array();
	}
}

?>