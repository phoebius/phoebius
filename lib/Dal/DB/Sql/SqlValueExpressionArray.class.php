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
 * Represents a list of ISqlValueExpression
 *
 * @see ISqlValueExpression
 * @ingroup Dal_DB_Sql
 */
class SqlValueExpressionArray extends TypedValueArray implements ISqlCastable
{
	/**
	 * @param array $array initial ISqlValueExpression objects to be added to the value list
	 */
	function __construct(array $values = array())
	{
		parent::__construct('ISqlValueExpression', $values);
	}

	function toDialectString(IDialect $dialect)
	{
		$compiledSlices = array();
		foreach ($this->getList() as $element) {
			$compiledSlices[] = $element->toDialectString($dialect);
		}

		$compiledString = join(', ', $compiledSlices);

		return $compiledString;
	}
}

?>