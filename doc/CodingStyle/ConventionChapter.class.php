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
 * Convention chapters
 * @ingroup CodingStyle
 */
final class ConventionChapter extends Enumeration
{
	const NAMING = 1;
	const FORMATTING = 2;
	const DOCUMENTING = 3;

	/**
	 * @return ConventionChapter
	 */
	static function naming()
	{
		return new self(self::NAMING);
	}

	/**
	 * @return ConventionChapter
	 */
	static function formatting()
	{
		return new self(self::FORMATTING);
	}

	/**
	 * @return ConventionChapter
	 */
	static function documenting()
	{
		return new self(self::DOCUMENTING);
	}
}

?>