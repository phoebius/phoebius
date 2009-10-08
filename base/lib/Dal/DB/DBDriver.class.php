<?php
/* ***********************************************************************************************
 *
 * Phoebius Framework
 *
 * **********************************************************************************************
 *
 * Copyright notice
 *
 ************************************************************************************************/

/**
 * Represents the database driver identifier
 * @ingroup Dal
 */
final class DBDriver extends Enumeration
{
	const MYSQL = 'MySql';
	const PGSQL = 'PgSql';
	const DUMMY = 'Dummy';

	/**
	 * @return DBDriver
	 */
	static function mysql()
	{
		return new self(self::MYSQL);
	}

	/**
	 * @return DBDriver
	 */
	static function pgsql()
	{
		return new self(self::PGSQL);
	}
}

?>