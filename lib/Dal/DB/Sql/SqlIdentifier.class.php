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
 * @ingroup Dal_DB_Sql
 */
class SqlIdentifier implements ISqlCastable
{
	private $id;

	function __construct($id)
	{
		$this->setId($id);
	}

	/**
	 * @return SqlIdentifier
	 */
	function setId($id)
	{
		Assert::isScalar($id);

		$this->id = $id;

		return $this;
	}

	/**
	 * @return string
	 */
	function getId()
	{
		return $this->id;
	}

	/**
	 * Casts an object to the SQL dialect string
	 * @return string
	 */
	function toDialectString(IDialect $dialect)
	{
		return $dialect->quoteIdentifier($this->id);
	}
}

?>