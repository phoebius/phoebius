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
 * Represents an ORDER BY direction
 * @ingroup Dal_DB_Sql
 */
final class OrderDirection extends Enumeration implements ISqlCastable
{
	const NONE = '';
	const ASC = 'ASC';
	const DESC = 'DESC';

	/**
	 * @return OrderDirection
	 */
	static function create($id)
	{
		return new self($id);
	}

	/**
	 * @return OrderDirection
	 */
	static function asc()
	{
		return new self(self::ASC);
	}

	/**
	 * @return OrderDirection
	 */
	static function desc()
	{
		return new self(self::DESC);
	}

	/**
	 * @return OrderDirection
	 */
	static function none()
	{
		return new self(self::NONE);
	}

	/**
	 * Casts an object to the SQL dialect string
	 * @return string
	 */
	function toDialectString(IDialect $dialect)
	{
		return $this->getValue();
	}
}

?>