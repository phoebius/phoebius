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
 * Represents a query for creating database tables.
 *
 * @ingroup Dal_DB_Query
 */
final class CreateTableQuery implements ISqlQuery
{
	/**
	 * @var DBTable
	 */
	private $table;

	/**
	 * @var array
	 */
	private $commaSeparatedQueryParts = array();

	/**
	 * @param DBTable $table a table object that represent an expected database table
	 */
	function __construct(DBTable $table)
	{
		$this->table = $table;
	}

	function toDialectString(IDialect $dialect)
	{
		$queryParts = array();
		$this->commaSeparatedQueryParts = array();

		$queryParts[] = 'CREATE TABLE ';
		$queryParts[] = $dialect->quoteIdentifier($this->table->getName());
		$queryParts[] = '(';

		$this->makeColumns($dialect);

		$queryParts[] = join(',', $this->commaSeparatedQueryParts);

		$queryParts[] = StringUtils::DELIM_STANDART;
		$queryParts[] = ');';

		return join('', $queryParts);
	}

	function getPlaceholderValues(IDialect $dialect)
	{
		return array ();
	}

	/**
	 * @return void
	 */
	private function makeColumns(IDialect $dialect)
	{
		foreach ($this->table->getColumns() as $column) {
			$this->makeColumn($column, $dialect);
		}
	}

	/**
	 * @return void
	 */
	private function makeColumn(DBColumn $column, IDialect $dialect)
	{
		$this->commaSeparatedQueryParts[] =
			StringUtils::DELIM_STANDART
			. "\t"
			. $column->toDialectString($dialect);
	}
}

?>