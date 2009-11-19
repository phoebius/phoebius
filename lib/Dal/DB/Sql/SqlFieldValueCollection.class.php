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
 * Represetns a key=>value collection if SqlValue. Field is a name of the column without specifying
 * its belonging to a table
 * @ingroup Dal_DB_Sql
 */
class SqlFieldValueCollection extends TypedCollection implements ISqlCastable
{
	/**
	 * @param array set of value to be appened to collection
	 */
	function __construct(array $array = array())
	{
		parent::__construct('SqlValue', $array);
	}

	/**
	 * Casts an object to the SQL dialect string
	 * @return string
	 */
	function toDialectString(IDialect $dialect)
	{
		$fieldValueCompiledPairs = array();

		foreach ($this->toArray() as $field => $value) {
			$fieldValueCompiledPairs[] =
				  $dialect->quoteIdentifier($field) . ' = '
				. $value->toDialectString($dialect);
		}

		$compiledCollection = join(', ', $fieldValueCompiledPairs);
		return $compiledCollection;
	}
}

?>