<?php
/* ***********************************************************************************************
 *
 * Phoebius Framework
 *
 * **********************************************************************************************
 *
 * Copyright notice
 *
 ************************************************************************************************/

/**
 * Represents a database query for deleting rows
 * @ingroup Query
 */
class DeleteQuery implements ISqlQuery
{
	/**
	 * @var string
	 */
	private $tableName;

	/**
	 * @var ISqlLogicalExpression
	 */
	private $condition;

	/**
	 * Creates an instance of {@link DeleteQuery}
	 * @param string $table
	 * @return DeleteQuery
	 */
	static function create($table)
	{
		return new self($table);
	}

	/**
	 * @param string $table
	 */
	function __construct($tableName)
	{
		Assert::isScalar($tableName);

		$this->tableName = $tableName;
	}

	/**
	 * Sets the query condition to fill the `WHERE` clause
	 * @return DeleteQuery an object itself
	 */
	function setCondition(ISqlLogicalExpression $logic = null)
	{
		$this->condition = $logic;

		return $this;
	}

	/**
	 * Gets the query condition or null if {@link ISqlLogicalExpression} is not set
	 * @return ISqlLogicalExpression|null
	 */
	function getCondition()
	{
		return $this->condition;
	}

	/**
	 * Gets the table name where the query should be performed
	 * @return string
	 */
	function getTableName()
	{
		return $this->tableName;
	}

	/**
	 * Casts an object to the plain string SQL query with database dialect
	 * @return string
	 */
	function toDialectString(IDialect $dialect)
	{
		$querySlices = array();

		$querySlices[] = 'DELETE FROM';
		$querySlices[] = $dialect->quoteIdentifier($this->tableName);

		if ($this->condition) {
			$querySlices[] = 'WHERE';
			$querySlices[] = $this->condition->toDialectString($dialect);
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