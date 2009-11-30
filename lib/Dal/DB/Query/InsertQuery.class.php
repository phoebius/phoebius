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
 * Represents the database query for inserting rows
 *
 * @ingroup Dal_DB_Query
 */
class InsertQuery extends RowModificationQuery implements ISqlQuery
{
	/**
	 * @var string
	 */
	private $table;

	/**
	 * InsertQuery static constructor
	 * @param string $table
	 * @return InsertQuery
	 */
	static function create($table)
	{
		return new self($table);
	}

	/**
	 * @param string $table table name
	 */
	function __construct($table)
	{
		Assert::isScalar($table);

		$this->tableName = $table;

		parent::__construct();
	}

	function toDialectString(IDialect $dialect)
	{
		$querySlices = array();

		$querySlices[] = 'INSERT INTO';
		$querySlices[] = $dialect->quoteIdentifier($this->tableName);

		$querySlices[] = '(';
		$querySlices[] = $this->getCompiledFields($dialect);
		$querySlices[] = ')';

		$querySlices[] = 'VALUES';
		$querySlices[] = '(';
		$querySlices[] = $this->getCompiledValues($dialect);
		$querySlices[] = ')';

		$compiledQuery = join(' ', $querySlices);
		return $compiledQuery;
	}

	function getPlaceholderValues(IDialect $dialect)
	{
		return array ();
	}

	/**
	 * @return string
	 */
	private function getCompiledFields(IDialect $dialect)
	{
		$list = new SqlFieldArray($this->getRow()->getKeys());

		return $list->toDialectString($dialect);
	}

	/**
	 * @return string
	 */
	private function getCompiledValues(IDialect $dialect)
	{
		$list = new SqlValueArray($this->getRow()->getValues());

		return $list->toDialectString($dialect);
	}
}

?>