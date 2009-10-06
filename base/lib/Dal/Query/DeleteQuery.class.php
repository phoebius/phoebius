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
	 * @var IDalExpression
	 */
	private $entityQuery;

	/**
	 * Creates an instance of {@link DeleteQuery}
	 * @param string $table
	 * @return DeleteQuery
	 */
	static function create($table, IDalExpression $expression = null)
	{
		return new self($table, $expression);
	}

	/**
	 * @param string $table
	 */
	function __construct($tableName, IDalExpression $expression = null)
	{
		Assert::isScalar($tableName);

		$this->tableName = $tableName;
		$this->setExpression($expression);
	}

	/**
	 * Sets the query condition to fill the `WHERE` clause
	 * @return DeleteQuery an object itself
	 */
	function setExpression(IDalExpression $expression = null)
	{
		$this->entityQuery = $expression;

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