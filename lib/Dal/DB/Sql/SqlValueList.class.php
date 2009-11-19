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
 * Represents the list of SqlValue
 * @ingroup Dal_DB_Sql
 */
class SqlValueList extends TypedValueList implements ISqlValueExpression
{
	function __construct(array $values = array())
	{
		parent::__construct('SqlValue', $values);
	}

	function toDialectString(IDialect $dialect)
	{
		$quotedValues = array();
		foreach ($this->getList() as $value) {
			$quotedValues[] = $value->toDialectString($dialect);
		}

		$joinedValues = join(', ', $quotedValues);

		return $joinedValues;
	}
}

?>