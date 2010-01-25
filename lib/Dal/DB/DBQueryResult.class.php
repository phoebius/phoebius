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
 * Represents a query result resource.
 *
 * @aux
 * @ingroup Dal_DB
 */
class DBQueryResult
{
	/**
	 * @var DB
	 */
	private $db;

	/**
	 * @var resource
	 */
	private $resource;

	/**
	 * @param DB $db
	 * @param resource $resultId
	 */
	function __construct(DB $db, $resource)
	{
		Assert::isTrue(is_resource($resource) || $resource === true);

		$this->db = $db;
		$this->resource = $resource;
	}

	/**
	 * Returns the result.
	 * @return resource|boolean
	 */
	function getResource()
	{
		return $this->resource;
	}
}

?>