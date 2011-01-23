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

if (!defined('PHOEBIUS_APP_ID')) {
	define('PHOEBIUS_APP_ID', 'default');
}

if (!defined('PHOEBIUS_TMP_ROOT')) {
	define(
		'PHOEBIUS_TMP_ROOT', 
		sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'Phoebius-v' . PHOEBIUS_VERSION . '-' . PHOEBIUS_APP_ID
	);
}
		
if (!defined('PHOEBIUS_LOADER')) {
	define('PHOEBIUS_LOADER', 'ondemand');
}

define('PHOEBIUS_SHORT_PRODUCT_NAME', 'Phoebius v'.PHOEBIUS_VERSION);
define('PHOEBIUS_FULL_PRODUCT_NAME', 'Phoebius framework v'.PHOEBIUS_VERSION);

define('PHOEBIUS_BASE_ROOT', dirname(__FILE__));

date_default_timezone_set('Europe/London');

/**
 * Should be appended with a dot
 */
define('PHOEBIUS_TYPE_EXTENSION', '.class.php');

$phoebiusNamespaces = array(
	'Core',
	'Core/Bootstrap',
	'Core/Exceptions',
	'Core/FS',
	'Core/Patterns',
	'Core/Types',
	'Core/Types/Complex',

	'Mvc',
	'Mvc/ActionResults',

	'UI',
	'UI/Mvc',
	'UI/Mvc/Presentation',
	'UI/Presentation',

	'Orm',
	'Orm/Dao',
	'Orm/Dao/Relationship',
	'Orm/Domain',
	'Orm/Domain/CodeGenerator',
	'Orm/Domain/Notation',
	'Orm/Domain/Notation/Xml',
	'Orm/Exceptions',
	'Orm/Model',
	'Orm/Query',
	'Orm/Query/Projections',
	'Orm/Types',

	'Utils',
	'Utils/Cipher',
	'Utils/Log',
	'Utils/Net',
	'Utils/Stream',
	'Utils/Xml',

	'App',
	'App/Server',
	'App/Web',
	'App/Web/Routing',

	'Dal',
	'Dal/DB',
	'Dal/DB/Exceptions',
	'Dal/DB/Generator',
	'Dal/DB/Query',
	'Dal/DB/Schema',
	'Dal/DB/Sql',
	'Dal/DB/Transaction',
	'Dal/DB/Type',
	'Dal/Expression',
	'Dal/Expression/LogicalOperators',
	
	'Dal/Cache',
	'Dal/Cache/Peers'
);

foreach ($phoebiusNamespaces as $namespace) {
	set_include_path(
		PHOEBIUS_BASE_ROOT . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . $namespace 
		. PATH_SEPARATOR . get_include_path()
	);	
}

require PHOEBIUS_BASE_ROOT . '/loader/' . PHOEBIUS_LOADER . '.loader.php';

Exceptionizer::getInstance()
	->register(E_ALL | E_STRICT, false, 'InternalOperationException')
	->setException(E_USER_ERROR, 'CompilationContextException')
	->setException(E_RECOVERABLE_ERROR, 'RecoverableErrorFactory');


try {
	$result = is_dir(PHOEBIUS_TMP_ROOT);
	
	if (!$result) {
		mkdir(PHOEBIUS_TMP_ROOT, 0777, true);
	}
}
catch (InternalOperationException $e) {
	die('Insufficient privileges to a temporary root (' . PHOEBIUS_TMP_ROOT . '): ' . $e->getMessage());
}
