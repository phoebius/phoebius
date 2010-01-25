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
 * Thrown when the database object requested by a query cannot be fetched
 * @ingroup Dal_DB_Exceptions
 */
class DBObjectNotFoundException extends DataNotFoundException
{
	/**
	 * @var ISqlQuery
	 */
	private $query;

	/**
	 * @param string $tableName
	 * @param string $columnName
	 */
	function __construct(ISqlQuery $query)
	{
		parent::__construct();

		$this->query = $query;
	}

	/**
	 * Returns the query
	 * @return ISqlQuery
	 */
	function getQuery()
	{
		return $this->query;
	}
}

?>