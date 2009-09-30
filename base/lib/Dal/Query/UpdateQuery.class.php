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
 * Represents a database query for updating rows
 * @ingroup Query
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
	private $condition;

	/**
	 * Creates an instance of {@link UpdateQuery}
	 * @param string $tableName
	 * @return UpdateQuery
	 */
	static function create($tableName)
	{
		return new self ($tableName);
	}

	/**
	 * @param string $table
	 */
	function __construct($tableName)
	{
		Assert::isScalar($tableName);

		$this->tableName = $tableName;
		$this->fields = new SqlFieldValueCollection();
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
	function setCondition(IDalExpression $logic)
	{
		$this->condition = $logic;

		return $this;
	}

	/**
	 * Gets the query condition or null if {@link IDalExpression} is not set
	 * @return IDalExpression|null
	 */
	function getCondition()
	{
		return $this->condition;
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