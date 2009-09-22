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