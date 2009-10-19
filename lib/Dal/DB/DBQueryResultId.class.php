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
 * Represents a query result resource identifer
 * @ingroup Dal_DB
 */
class DBQueryResultId
{
	/**
	 * @var DB
	 */
	private $db;

	/**
	 * @var resource
	 */
	private $resultId;

	/**
	 * @param DB $db
	 * @param resource $resultId
	 */
	function __construct(DB $db, $resultId)
	{
		Assert::isTrue(is_resource($resultId) || $resultId === true);

		$this->db = $db;
		$this->resultId = $resultId;
	}

	/**
	 * Checks whether the result id conforms the query that run the specified database hanle
	 * @return boolean
	 */
	function isValid(DB $db)
	{
		return $db === $this->db;
	}

	/**
	 * Returns the result id
	 */
	function getResultId()
	{
		return $this->resultId;
	}
}

?>