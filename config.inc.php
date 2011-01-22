<?php
/* ***********************************************************************************************
 *
 * Phoebius Framework
 *
 * **********************************************************************************************
 *
 * Copyright (c) 2011 Scand Ltd.
 *
 * This program is free software; you can redistribute it and/or modify it under the terms
 * of the GNU Lesser General Public License as published by the Free Software Foundation;
 * either version 3 of the License, or (at your option) any later version.
 *
 * You should have received a copy of the GNU Lesser General Public License along with
 * this program; if not, see <http://www.gnu.org/licenses/>.
 *
 ************************************************************************************************/

define('PHOEBIUS_VERSION', '1.4.0-dev');
define('PHOEBIUS_SHORT_PRODUCT_NAME', 'Phoebius v'.PHOEBIUS_VERSION);
define('PHOEBIUS_FULL_PRODUCT_NAME', 'Phoebius framework v'.PHOEBIUS_VERSION);

define('PHOEBIUS_BASE_ROOT', dirname(__FILE__));

if (!defined('PHOEBIUS_APP_ID')) {
	define('PHOEBIUS_APP_ID', 'default');
}

if (!defined('PHOEBIUS_TMP_ROOT')) {
	define(
		'PHOEBIUS_TMP_ROOT', 
		sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'Phoebius-v' . PHOEBIUS_VERSION . '-' . PHOEBIUS_APP_ID
	);
	
	if (!is_dir(PHOBEBIUS_TMP_ROOT)) {
		mkdir(PHOEBIUS_TMP_ROOT, 700, true);
	}
}

/**
 * Should be appended with a dot
 */
define('PHOEBIUS_TYPE_EXTENSION', '.class.php');

set_include_path(PHOEBIUS_BASE_ROOT . PATH_SEPARATOR . 'lib' . PATH_SEPARATOR . get_include_path());

$Iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(PHOEBIUS_BASE_ROOT . DIRECTORY_SEPARATOR . 'lib'));

foreach ($Iterator as $Path) {
	set_include_path($Iterator->getRealPath() . PATH_SEPARATOR . get_include_path());
}
		
if (defined('PHOEBIUS_LOADER')) {
	require 'loader/ondemand.loader.php';
}

Exceptionizer::getInstance()
	->register(E_ALL | E_STRICT, false, 'InternalOperationException')
	->setException(E_USER_ERROR, 'CompilationContextException')
	->setException(E_RECOVERABLE_ERROR, 'RecoverableErrorFactory');

