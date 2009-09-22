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