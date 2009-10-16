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
 * @ingroup Test
 */
final class AllTests
{
	const GLOBALS_TEST_PATHS_INDEX = 'testPaths';

	public static $testPaths = array();

	static function main()
	{
		PHPUnit_TextUI_TestRunner::run(self::suite());
	}

	static function suite()
	{
		if (isset($GLOBALS[self::GLOBALS_TEST_PATHS_INDEX])) {
			self::$testPaths = $GLOBALS[self::GLOBALS_TEST_PATHS_INDEX];
		}

		Exceptionizer::getInstance()->register(E_ALL | E_STRICT, false, 'InternalOperationException');
		return new PhoebiusTestSuite(self::$testPaths);
	}
}

if (!defined('PHOEBIUS_CONFIG_LOADED')) {
	require_once '../../etc/appless.init.php';
}

?>