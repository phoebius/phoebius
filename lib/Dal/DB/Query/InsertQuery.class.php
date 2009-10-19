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
 * @ingroup Dal_DB_Query
 */
class InsertQuery implements ISqlQuery
{
	/**
	 * @var string
	 */
	private $tableName;

	/**
	 * @var SqlFieldValueCollection
	 */
	private $fields;

	/**
	 * Creates an instance of {@link DeleteQuery}
	 * @param string $table
	 * @return InsertQuery
	 */
	static function create($table, SqlFieldValueCollection $fvc = null)
	{
		return new self($table, $fvc);
	}

	/**
	 * @param string $table table name
	 */
	function __construct($table, SqlFieldValueCollection $fvc = null)
	{
		Assert::isScalar($table);

		$this->tableName = $table;
		$this->fields =
			$fvc
				? $fvc
				: new SqlFieldValueCollection();
	}

	/**
	 * Adds a custom field=>value set
	 * @return InsertQuery an object itself
	 */
	function setFieldValueCollection(SqlFieldValueCollection $set)
	{
		$this->fields = $set;

		return $this;
	}

	/**
	 * Returns a field=>value to be inserted with a database query
	 * @return SqlFieldValueCollection
	 */
	function getFieldValueCollection()
	{
		return $this->fields;
	}

	/**
	 * Adds a custom field and it's corresponding value to the field=>value set
	 * @param string $field
	 * @param SqlValue $value
	 * @return InsertQuery an object itself
	 */
	function addFieldAndValue($field, SqlValue $value)
	{
		$this->fields->add($field, $value);

		return $this;
	}

	/**
	 * Casts an object to the plain string SQL query with database dialect
	 * @return string
	 */
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

	/**
	 * @see ISqlQuery::getCastedParameters()
	 *
	 * @param IDialect $dialect
	 * @return array
	 */
	function getCastedParameters(IDialect $dialect)
	{
		return array ();
	}

	/**
	 * @return string
	 */
	private function getCompiledFields(IDialect $dialect)
	{
		return
			SqlFieldList::create(
				$this->fields->getFields()
			)
			->toDialectString($dialect);
	}

	/**
	 * @return string
	 */
	private function getCompiledValues(IDialect $dialect)
	{
		return
			SqlValueList::create(
				$this->fields->getValues()
			)
			->toDialectString($dialect);
	}
}

?>