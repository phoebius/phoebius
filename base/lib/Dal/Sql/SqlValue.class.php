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
 * An abstract representation of SQL-castable value
 * @ingroup Sql
 */
abstract class SqlValue implements ISqlValueExpression
{
	/**
	 * @var mixed|null
	 */
	private $value;

	/**
	 * @param mixed $value
	 */
	function __construct($value)
	{
		$this->setValue($value);
	}

	/**
	 * Gets the value to be casted to SQL value
	 * @return mixed
	 */
	function getValue()
	{
		return $this->value;
	}

	/**
	 * Returns the value
	 * @warning value can be null too!
	 * @return mixed|null
	 */
	function setValue($value = null)
	{
		$this->value = $value;
	}

	/**
	 * Casts an object to the SQL dialect string
	 * @return string
	 */
	function toDialectString(IDialect $dialect)
	{
		return $dialect->quoteValue($this->value);
	}
}

?>