<?php
/* ***********************************************************************************************
 *
 * Phoebius Framework
 *
 * **********************************************************************************************
 *
 * Copyright (c) 2009 Scand Ltd.
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
 * Represents the database driver identifier
 * @ingroup Dal_DB
 */
final class DBDriver extends Enumeration
{
	/**
	 * Defines MySQL
	 */
	const MYSQL = 'MySql';

	/**
	 * Defines PostgreSQL
	 */
	const PGSQL = 'PgSql';

	/**
	 * Defines dummy stub
	 */
	const DUMMY = 'Dummy';

	/**
	 * Gets MySQL identifier
	 * @return DBDriver
	 */
	static function mysql()
	{
		return new self(self::MYSQL);
	}

	/**
	 * Gets PostgreSQL identifier
	 * @return DBDriver
	 */
	static function pgsql()
	{
		return new self(self::PGSQL);
	}
}

?>