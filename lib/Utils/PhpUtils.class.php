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
 * @ingroup Utils
 */
final class PhpUtils extends StaticClass
{
	/**
	 * Implemented interface of switching a setting `magic_quotes_gpc` at runtime.
	 * @return int
	 * @version 3
	 * @since 03/12/2005
	 * @return void
	 */
	static function turnOffMagicQuotesGpc()
	{
		static $processed = false;

		if ($processed) {
			return;
		}
		$processed = true;

		if ( get_magic_quotes_gpc() != 0 ) {
			$superGlobals = array('_GET', '_POST', '_COOKIE', '_REQUEST');
			foreach ($superGlobals as $superGlobal) {
				ArrayUtils::iterateCallback(
				    $GLOBALS[$superGlobal],
				    array(
    					'addslashes'
    				)
    			);
			}
		}
	}

	/**
	 * Determines whether type (class or interface) is already defined. Autoloading is ommited
	 * @return boolean
	 */
	static function typeExists($typeName)
	{
		return
			   (class_exists($typeName, false))
			|| (interface_exists($typeName, false));
	}


	/**
	 * Returns current time in stopwatchonds or calculates the difference between current time and
	 * provided time
	 * @return float
	 */
	static function stopwatch($time = 0)
	{
		return microtime(true) - $time;
	}
}

?>