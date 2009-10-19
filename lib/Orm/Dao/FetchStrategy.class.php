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
 * @ingroup Orm_Dao
 */
final class FetchStrategy extends Enumeration
{
	/**
	 * fetching is proceded by request
	 */
	const LAZY = 1;

	/**
	 * not actually "CASCADE" because referential Daos could have another FetchStrategy, e.g. LAZY
	 */
	const CASCADE = 2;

	/**
	 * @return FetchStrategy
	 */
	static function lazy()
	{
		return new self(self::LAZY);
	}

	/**
	 * @return FetchStrategy
	 */
	static function cascade()
	{
		return new self(self::CASCADE);
	}
}

?>