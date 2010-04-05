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

define('PHOEBIUS_VERSION', '1.2.0-dev');

$initializeRootDirectory = str_replace(array('/','\\'), DIRECTORY_SEPARATOR, dirname(__FILE__));
$initializeRootDirectory = explode(DIRECTORY_SEPARATOR, $initializeRootDirectory);
array_pop($initializeRootDirectory);
array_pop($initializeRootDirectory);
$baseRootDirectory = implode(DIRECTORY_SEPARATOR, $initializeRootDirectory);

/**
 * Absolute path to the directory where the application core is situated
 */
define('PHOEBIUS_BASE_ROOT', $baseRootDirectory);

/**
 * Defines the state when framework core is initialized
 */
define('PHOEBIUS_INITIALIZED', true);

define('PHOEBIUS_SHORT_PRODUCT_NAME', 'Phoebius v.'.PHOEBIUS_VERSION);
define('PHOEBIUS_FULL_PRODUCT_NAME', 'Phoebius Framework v.'.PHOEBIUS_VERSION);

if (!defined('APP_AREA')) {
	/**
	 * Application area id
	 */
	define('APP_AREA', 'Default');
}

if (!defined('APP_TMP_ROOT')) {
	/**
	 * Application tmp storage
	 */
	define(
		'APP_TMP_ROOT',
		defined('APP_ROOT')
			? APP_ROOT . DIRECTORY_SEPARATOR . 'tmp'
			: PHOEBIUS_BASE_ROOT . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR . sha1(defined('APP_GUID') ? APP_GUID : APP_AREA)
	);
}

if (!defined('APP_GUID')) {
	/**
	 * Application GUID
	 */
	define(
		'APP_GUID',
		hexdec(substr(sha1(APP_TMP_ROOT), 0, 8))
	);
}

// application slot configuration

define ('SLOT_CONFIGURATION_FLAG_DEVELOPMENT', 0x000001);
define ('SLOT_CONFIGURATION_FLAG_TEST', 0x000002);
define ('SLOT_CONFIGURATION_FLAG_PRODUCTION', 0x000004);

define ('SLOT_CONFIGURATION_SEVERITY_VERBOSE', 0x000010);
define ('SLOT_CONFIGURATION_SEVERITY_OPTIMIZE', 0x000020);

define(
	'SLOT_PRESET_DEVELOPMENT',
	  SLOT_CONFIGURATION_FLAG_DEVELOPMENT
	| SLOT_CONFIGURATION_SEVERITY_VERBOSE
);
define(
	'SLOT_PRESET_TEST',
	  SLOT_CONFIGURATION_FLAG_TEST
	| SLOT_CONFIGURATION_SEVERITY_VERBOSE
	| SLOT_CONFIGURATION_SEVERITY_OPTIMIZE
);
define(
	'SLOT_PRESET_PRODUCTION',
	  SLOT_CONFIGURATION_FLAG_PRODUCTION
	| SLOT_CONFIGURATION_SEVERITY_OPTIMIZE
);


if (!defined('APP_SLOT')) {
	define(
		'APP_SLOT',
		isset($_ENV['PHOEBIUS_APP_SLOT'])
			? $_ENV['PHOEBIUS_APP_SLOT']
			: (
				isset($_SERVER['PHOEBIUS_APP_SLOT'])
					? $_SERVER['PHOEBIUS_APP_SLOT']
					: 'default'
			)
	);
}

/**
 * Should be appended with a dot
 */
define('PHOEBIUS_TYPE_EXTENSION', '.class.php');

//////////////////////////////////////////////////////////////////////////////////////////////////

// no comment
set_magic_quotes_runtime(0);

// Set GMT timezone for compatibility (if you forgot to do that later)
if (!isset($_ENV['TZ'])) {
	date_default_timezone_set('Europe/London');
}

//////////////////////////////////////////////////////////////////////////////////////////////////

// FIXME: wrap the linear inclusion of core files with the code that caches all those files
// inside separate file, that possibly can be used both by Autoloader
$classes = array
(
	'IAutoloader' => 'Core/Bootstrap',
	'StaticClass' => 'Core/Patterns',
	'LazySingleton' => 'Core/Patterns',
	'Assert' => 'Core',
	'InternalSegmentCache' => 'Core/Bootstrap',
	'IClassResolver' => 'Core/Bootstrap',
	'ClassResolver' => 'Core/Bootstrap',
	'CSharpStyleClassResolver' => 'Core/Bootstrap',
	'PathResolver' => 'Core/Bootstrap',
	'Autoloader' => 'Core/Bootstrap',
	'Exceptionizer' => 'Core/Bootstrap',
	'IErrorExceptionFactory' => 'Core/Bootstrap',
	'ApplicationException' => 'Core/Exceptions',
	'ArgumentException' => 'Core/Exceptions',
	'ApplicationException' => 'Core/Exceptions',
	'ExecutionContextException' => 'Core/Exceptions',
	'InternalOperationException' => 'Core/Exceptions',
	'RecoverableErrorFactory' => 'Core/Bootstrap',
	'CompilationContextException' => 'Core/Exceptions',
	'TypeUtils' => 'Utils'
);

foreach ($classes as $classname => $classDirectory)
{
	require (
		PHOEBIUS_BASE_ROOT . DIRECTORY_SEPARATOR .
		'lib' . DIRECTORY_SEPARATOR .
		$classDirectory . DIRECTORY_SEPARATOR .
		$classname . PHOEBIUS_TYPE_EXTENSION
	);
}

//////////////////////////////////////////////////////////////////////////////////////////////////

Exceptionizer::getInstance()
	->register(E_ALL | E_STRICT, false, 'InternalOperationException')
	->setException(E_USER_ERROR, 'CompilationContextException')
	->setException(E_RECOVERABLE_ERROR, 'RecoverableErrorFactory');

set_include_path(PHOEBIUS_BASE_ROOT . '/lib' . PATH_SEPARATOR . get_include_path());

Autoloader::getInstance()
	->addResolver(
		CSharpStyleClassResolver::create()
			->setExtension(PHOEBIUS_TYPE_EXTENSION)
	)
	->register();

?>