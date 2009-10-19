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
 * Thrown every time the PostgreSQL raises an error on the passed query
 * @ingroup Dal_DB_Exceptions
 */
final class PgSqlQueryException extends DBQueryException
{
	private $sqlState;

	/**
	 * @param ISqlQuery $query
	 * @param string $errormsg
	 * @param scalar $errorno
	 */
	function __construct(ISqlQuery $query, $errormsg, $errorno)
	{
		$this->sqlState = $errorno;

		parent::__construct($query, $errormsg, 0);
	}

	/**
	 * @return PgSqlError
	 */
	function getSystemMessage()
	{
		return PgSqlError::create($this->sqlState);
	}
}

?>