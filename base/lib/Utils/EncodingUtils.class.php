<?php
/* ***********************************************************************************************
 *
 * Phoebius Framework
 *
 * **********************************************************************************************
 *
 * Copyright notice
 *
 ************************************************************************************************/

/**
 * Encoding utilities
 * @ingroup Utils
 */
final class EncodingUtils extends StaticClass
{
	static function isUtf8($str)
	{
		// ^(?:[\x00-\x7f]|[\xc0-\xdf][\x80-\xbf]|[\xe0​-\xef][\x80-\xbf][\x80-\xbf]|[\xf0-\xf7][\x80-\xbf​][\x80-\xbf][\x80-\xbf]+)$
		return $str === mb_convert_encoding(mb_convert_encoding($str, "UTF-32", "UTF-8"), "UTF-8", "UTF-32");
	}
}

?>