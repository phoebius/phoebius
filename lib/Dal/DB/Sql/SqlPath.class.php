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
 * Represents a path of SQL identifiers that unambiguously refer to the database object.
 *
 * This identifier would be correctly quoted while translating to SQL
 *
 * Example:
 * @code
 * // result is: "public"."table"
 * $path = new SqlPath('public', 'table');
 *
 * // the same:
 * $path = new SqlPath('public.table');
 * @endcode
 *
 * @ingroup Dal_DB_Sql
 */
final class SqlPath implements ISqlCastable
{
	private $path = array();

	/**
	 * @param string ... list of identifier chunks
	 */
	function __construct($chunk)
	{
		$args = func_get_args();

		if (sizeof($args) == 1) {
			$this->path = explode('.', $args[0]);
		}
		else if (sizeof($args) > 1) {
			$this->path = $args;
		}
	}

	function toDialectString(IDialect $dialect)
	{
		$processedPathChunks = array();

		foreach ($this->path as $pathChunk) {
			$processedPathChunks[] = $dialect->quoteIdentifier($pathChunk);
		}

		return join('.', $processedPathChunks);
	}
}

?>