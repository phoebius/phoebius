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

require dirname(__FILE__) . '/appless.init.php';

// This is needed because ZendPhpUnit launcher does not check the indexes of
// arrays it accesses to :( any access to unexistant index throws an exception
// TODO 1: add halt/unhalt() method pair to avoid direct register()/unregister() calls
//         (such calls are expensive)
// TODO 2: add "kernel" mode - an abstraction over Exceptionizer::halt()/unhalt()
Exceptionizer::getInstance()->unregister();

AllTests::$testPaths = array(
	PHOEBIUS_BASE_ROOT . DIRECTORY_SEPARATOR . 'tests'
);

set_include_path(
	PHOEBIUS_BASE_ROOT . DIRECTORY_SEPARATOR . 'tests'
	. PATH_SEPARATOR . get_include_path()
);

?>