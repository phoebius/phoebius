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
 * Represents the list of ISqlValueExpression
 * @ingroup Dal_DB_Sql
 * @see ISqlValueExpression
 */
class SqlValueExpressionArray extends TypedValueArray implements ISqlCastable
{
	/**
	 * @return SqlValueExpressionArray
	 */
	static function create(array $values = array())
	{
		return new self ($values);
	}

	function __construct(array $values = array())
	{
		parent::__construct('ISqlValueExpression', $values);
	}

	/**
	 * Casts an object to the SQL dialect string
	 * @return string
	 */
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