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
 * Represents a list value expressions (i.e. the list of {@link ISqlValueExpression})
 * @ingroup Dal_DB_Sql
 * @see ISqlValueExpression
 */
class SqlValueExpressionList extends TypedValueList implements ISqlCastable
{
	/**
	 * Creates an instance of {@link SqlValueExpression}
	 * @param array $initialValueExpressions list of initial {@link ISqlValueExpression} to be imported
	 * @return SqlValueExpression
	 */
	static function create(array $initialValueExpressions = array())
	{
		return new self ($initialValueExpressions);
	}

	/**
	 * Append a value expression to the list
	 * @return SqlValueExpression an object itself
	 */
	function add(ISqlValueExpression $element)
	{
		$this->append($element);

		return $this;
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

	/**
	 * Determines whether the specified value is of valid type supported by the list implementation
	 * @return boolean
	 */
	protected function isValueOfValidType($value)
	{
		return ($value instanceof ISqlValueExpression);
	}
}

?>