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
 * @ingroup Dao
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