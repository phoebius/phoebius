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
 * Thrown by the database procedure (raised exception)
 * @ingroup Dal_DB_Exceptions
 */
class DBProcedureException extends DBException
{
	/**
	 * @var string
	 */
	private $query;

	/**
	 * @param ISqlQuery $query
	 * @param string $errormsg
	 * @param integer $errorno
	 */
	function __construct(ISqlQuery $query, $errormsg)
	{
		Assert::isScalar($errormsg);

		parent::__construct($errormsg);
		$this->query = $query;
	}

	/**
	 * @return ISqlQuery
	 */
	function getQuery()
	{
		return $this->query;
	}
}

?>