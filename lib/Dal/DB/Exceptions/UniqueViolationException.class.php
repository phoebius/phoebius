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
 * Thrown in case the INSERT/UPDATE/DELETE query violates the database constraints (foreign
 * keys, unique indicies, etc)
 * @ingroup Dal_DB_Exceptions
 */
class UniqueViolationException extends DBQueryException
{
	/**
	 * @param ISqlQuery $query
	 * @param string $errormsg
	 */
	function __construct(ISqlQuery $query, $errormsg)
	{
		parent::__construct($query, $errormsg, 0);
	}
}

?>