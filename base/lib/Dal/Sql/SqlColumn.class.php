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
 * Represents a sql column (that can reference to its own table)
 * @ingroup Sql
 */
class SqlColumn implements ISqlValueExpression
{
	/**
	 * @var string
	 */
	private $fieldName;

	/**
	 * @var string
	 */
	private $tableName;

	/**
	 * Creates an instance of {@link SqlColumn}
	 * @param string $fieldName
	 * @param string $tableName
	 * @return SqlColumn
	 */
	static function create($fieldName, $tableName = null)
	{
		return new self ($fieldName, $tableName);
	}

	/**
	 * @param string $fieldName
	 * @param string $tableName
	 */
	function __construct($fieldName, $tableName = null)
	{
		$this->setFieldName($fieldName);
		$this->setTableName($tableName);
	}

	/**
	 * Gets the string representing a field name
	 * @return string
	 */
	function getFieldName()
	{
		return $this->fieldName;
	}

	/**
	 * Sets the field name
	 * @param string $fieldName
	 * @return SqlColumn an object itself
	 */
	function setFieldName($fieldName)
	{
		Assert::isScalar($fieldName);

		$this->fieldName = $fieldName;

		return $this;
	}

	/**
	 * Sets the table name to which the field belongs to
	 * @param string|null $tableName
	 * @return SqlColumn an object itself
	 */
	function setTableName($tableName = null)
	{
		Assert::isScalarOrNull($tableName);

		$this->tableName = $tableName;

		return $this;
	}

	/**
	 * Gets the string representing a table name to which the field belongs to
	 * @return string|null
	 */
	function getTableName()
	{
		return $this->tableName;
	}

	/**
	 * Casts an object to the SQL dialect string
	 * @return string
	 */
	function toDialectString(IDialect $dialect)
	{
		$sqlSlices = array();
		if ($this->tableName)
		{
			$sqlSlices[] = $dialect->quoteIdentifier($this->tableName);
		}

		$sqlSlices[] = $dialect->quoteIdentifier($this->fieldName);

		$identifier = join('.', $sqlSlices);
		return $identifier;
	}
}

?>