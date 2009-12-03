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
 * Encoding utilities.
 *
 * @ingroup Utils
 */
final class EncodingUtils extends StaticClass
{
	/**
	 * Whether string is in UTF-8
	 * @param string $str
	 * @return boolean
	 */
	static function isUtf8($str)
	{
		// ^(?:[\x00-\x7f]|[\xc0-\xdf][\x80-\xbf]|[\xe0​-\xef][\x80-\xbf][\x80-\xbf]|[\xf0-\xf7][\x80-\xbf​][\x80-\xbf][\x80-\xbf]+)$
		return $str === mb_convert_encoding(mb_convert_encoding($str, "UTF-32", "UTF-8"), "UTF-8", "UTF-32");
	}
}

?>