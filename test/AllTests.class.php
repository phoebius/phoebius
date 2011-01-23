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
 * @ingroup Test
 */
final class AllTests
{
	static function main()
	{
		//Exceptionizer::getInstance()->register(E_ALL | E_STRICT, false, 'InternalOperationException');
		require_once '../etc/phpunit.init.php';
		PHPUnit_TextUI_TestRunner::run(self::suite());
	}

	static function suite()
	{
		$suite = new PhoebiusTestSuite();
		
		return $suite;
	}
}

?>