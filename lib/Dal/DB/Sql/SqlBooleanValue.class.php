<?php
/* ***********************************************************************************************
 *
 * Phoebius Framework
 *
 * **********************************************************************************************
 *
 * Copyright (c) 2010 Scand Ltd.
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
 * An abstract representation of SQL-castable value
 * @ingroup Dal_DB_Sql
 */
class SqlBooleanValue extends SqlValue
{
	function toDialectString(IDialect $dialect)
	{
		$value = $this->getValue();

		if (!is_null($value)) {
			switch ($dialect->getDBDriver()->getValue()) {
				case DBDriver::PGSQL: {
					$value = $value ? 't' : 'f';
				}

				case DBDriver::MYSQL:
				default: {
					$value = $value ? 1 : 0;
				}
			}

			return $dialect->quoteValue($value);
		}

		return parent::toDialectString($dialect);
	}
}

?>