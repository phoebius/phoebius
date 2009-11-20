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
 * Represents a list of fields, that can be casted to SQL value set
 * @ingroup Dal_DB_Sql
 */
class SqlFieldList extends ValueArray implements ISqlCastable
{
	function toDialectString(IDialect $dialect)
	{
		$quotedFields = array();
		foreach ($this->getList() as $field) {
			$quotedFields[] = $dialect->quoteIdentifier($field);
		}

		$joinedFields = join(', ', $quotedFields);

		return $joinedFields;
	}
}

?>