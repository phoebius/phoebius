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
 * Represents a database identifier. This identifier would be correctly quoted while translating
 * to SQL
 *
 * @ingroup Dal_DB_Sql
 */
final class SqlIdentifier implements ISqlValueExpression
{
	private $id;

	/**
	 * @param string $id textual representation of the identifier
	 */
	function __construct($id)
	{
		Assert::isScalar($id);

		$this->id = $id;
	}

	/**
	 * Gets the textual representation of the database identifier
	 *
	 * @return string
	 */
	function getId()
	{
		return $this->id;
	}

	function toDialectString(IDialect $dialect)
	{
		return $dialect->quoteIdentifier($this->id);
	}
}

?>