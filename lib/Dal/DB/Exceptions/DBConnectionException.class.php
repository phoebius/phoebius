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
 * Thrown when the connection to DB fails
 * @ingroup Dal_DB_Exceptions
 */
class DBConnectionException extends DBException
{
	/**
	 * @var DB
	 */
	private $db;

	/**
	 * @param DB $dbHandle DB with failed connection parameters
	 * @param string $errorMessage actual error string
	 */
	function __construct(DB $dbHandle, $errorMessage)
	{
		parent::__construct($errorMessage);

		$this->db = $dbHandle;
	}

	/**
	 * Returns the db handle with connection parameters that failed
	 * @return DB
	 */
	function getDB()
	{
		return $this->db;
	}
}

?>