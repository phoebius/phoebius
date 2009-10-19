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
 * Represents a database query for updating rows
 * @ingroup Dal_DB_Query
 */
class UpdateQuery implements ISqlQuery
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
	 * @var IDalExpression
	 */
	private $entityQuery;

	/**
	 * Creates an instance of {@link UpdateQuery}
	 * @param string $tableName
	 * @return UpdateQuery
	 */
	static function create(
			$tableName,
			SqlFieldValueCollection $fvc = null,
			IDalExpression $expression = null
		)
	{
		return new self ($tableName, $fvc, $expression);
	}

	/**
	 * @param string $table
	 */
	function __construct(
			$tableName,
			SqlFieldValueCollection $fvc = null,
			IDalExpression $expression = null
		)
	{
		Assert::isScalar($tableName);

		$this->tableName = $tableName;
		$this->fields =
			$fvc
				? $fvc
				: new SqlFieldValueCollection();
		if ($expression) {
			$this->setExpression($expression);
		}
	}

	/**
	 * Adds a custom field and it's corresponding value to the field=>value set
	 * @param string $field
	 * @param SqlValue $value
	 * @return UpdateQuery an object itself
	 */
	function addFieldAndValue($field, SqlValue $value)
	{
		$this->fields->add($field, $value);

		return $this;
	}

	/**
	 * Adds a custom field=>value set
	 * @return UpdateQuery an object itself
	 */
	function setFieldValueCollection(SqlFieldValueCollection $set)
	{
		$this->fields = $set;

		return $this;
	}

	/**
	 * Sets the query condition to fill the `WHERE` clause
	 * @return DeleteQuery an object itself
	 */
	function setExpression(IDalExpression $logic)
	{
		$this->entityQuery = $logic;

		return $this;
	}

	/**
	 * Gets the query condition or null if {@link IDalExpression} is not set
	 * @return IDalExpression|null
	 */
	function getExpression()
	{
		return $this->entityQuery;
	}

	/**
	 * Casts an object to the plain string SQL query with database dialect
	 * @return string
	 */
	function toDialectString(IDialect $dialect)
	{
		$querySlices = array();

		$querySlices[] = 'UPDATE';
		$querySlices[] = $dialect->quoteIdentifier($this->tableName);

		$querySlices[] = 'SET';
		$querySlices[] = $this->fields->toDialectString($dialect);

		if ($this->entityQuery) {
			$querySlices[] = 'WHERE';
			$querySlices[] = $this->entityQuery->toDialectString($dialect);
		}

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
}

?>