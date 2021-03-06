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
 * Represents a query for altering tables for creating indexes
 *
 * @ingroup Dal_DB_Query
 */
final class CreateIndexQuery implements ISqlQuery
{
	/**
	 * @var DBIndex
	 */
	private $index;

	/**
	 * @param DBTable $table a table object that represent an expected database table
	 * @param DBIndex $index index to be created
	 */
	function __construct(DBIndex $index)
	{
		$this->index = $index;
	}

	function toDialectString(IDialect $dialect)
	{
		return
			'CREATE ' . $this->index->toDialectString($dialect)
			. ';';
	}

	function getPlaceholderValues(IDialect $dialect)
	{
		return array();
	}
}

?>