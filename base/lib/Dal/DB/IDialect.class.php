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
 * Represents a SQL dialect for the database
 * @ingroup Dal
 */
interface IDialect
{
	/**
	 * Quotes a string as SQL identifier
	 * @param string $identifier
	 * @return string
	 */
	function quoteIdentifier($identifier);

	/**
	 * Quotes a string as SQL value
	 * @param string $value
	 * @return string
	 */
	function quoteValue($value);

	/**
	 * @return DBDriver
	 */
	function getDBDriver();

	/**
	 * @return string
	 */
	function getTypeRepresentation(DBType $dbType);

	/**
	 * @return array
	 */
	function getTableQuerySet(DBTable $table);
}

?>