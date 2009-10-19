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
 * Represents a SQL dialect for the database
 * @ingroup Dal_DB
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