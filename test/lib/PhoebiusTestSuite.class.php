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
class PhoebiusTestSuite extends PHPUnit_Framework_TestSuite
{
	function __construct($suitsRoot)
	{
		parent::__construct();

		$this->setName(__CLASS__);

		$Directory = new RecursiveDirectoryIterator('suits');
		$Iterator = new RecursiveIteratorIterator($Directory);
		$Regex = new RegexIterator($Iterator, '/^.+\.test\.php$/i', RecursiveRegexIterator::GET_MATCH);
		
		foreach ($Regex as $Path) {
			$this->addTestFile($Path);
		}
	}
}


?>