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
 * PK = Unique + NotNull
 * @ingroup Dal_DB_Schema
 */
class DBPrimaryKeyConstraint extends DBUniqueConstraint
{
	/**
	 * @return DBPrimaryKeyConstraint
	 */
	static function create(array $columns = array())
	{
		return new self ($columns);
	}

	/**
	 * @return string
	 */
	protected function getHead(IDialect $dialect)
	{
		return 'PRIMARY KEY';
	}
}

?>