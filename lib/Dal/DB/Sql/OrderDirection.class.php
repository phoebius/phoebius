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
 * Represents the ordering direction
 *
 * @ingroup Dal_DB_Sql
 */
final class OrderDirection extends Enumeration implements ISqlCastable
{
	/**
	 * Ascending
	 */
	const ASC = 'ASC';

	/**
	 * Descending
	 */
	const DESC = 'DESC';

	/**
	 * Creates an instance of ascending OrderDirection
	 *
	 * @return OrderDirection
	 */
	static function asc()
	{
		return new self(self::ASC);
	}

	/**
	 * Creates an instance of descending OrderDirection
	 *
	 * @return OrderDirection
	 */
	static function desc()
	{
		return new self(self::DESC);
	}

	function toDialectString(IDialect $dialect)
	{
		return $this->getValue();
	}
}

?>