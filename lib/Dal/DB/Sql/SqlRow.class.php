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
 * Represents a key=>value associative array of ISqlValueExpression.
 * @ingroup Dal_DB_Sql
 */
final class SqlRow extends TypedCollection implements ISqlCastable
{
	/**
	 * @param array set of value to be appened to the collection
	 */
	function __construct(array $array = array())
	{
		parent::__construct('ISqlValueExpression', $array);
	}

	function toDialectString(IDialect $dialect)
	{
		$fieldValueCompiledPairs = array();

		foreach ($this->toArray() as $field => $value) {
			$fieldValueCompiledPairs[] =
				  $dialect->quoteIdentifier($field) . '='
				. $value->toDialectString($dialect);
		}

		$compiledCollection = join(', ', $fieldValueCompiledPairs);
		return $compiledCollection;
	}
}

?>