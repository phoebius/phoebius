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

//
// Overriddable constants:
//
// * PHOEBIUS_APP_ID - should be set in case when different application
//						use the same Phoebius framework distribtuib
// * PHOEBIUS_TMP_ROOT - path to the tmp directory
// * PHOEBIUS_LOADER - id of the Phoebius loader to use: ondemand, pathcache, classcache.
//						See the loader/ directory, and consider overridable constants of
//						desireable loader.
// * PHOEBIUS_APP_ROOT - directory where the application resides; by default is the same as the
//							the directory where Phoebius framework distribution resides
// * PHOEBIUS_APP_VIEWS_ROOT - views' directory; PHOEBIUS_APP_ROOT/views by default.
//

define('PHOEBIUS_VERSION', '1.4.0-dev');
define('PHOEBIUS_BASE_ROOT', dirname(__FILE__));

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

if (!defined('PHOEBIUS_APP_ROOT')) {
	define('PHOEBIUS_APP_ROOT', PHOEBIUS_BASE_ROOT);
}

if (!defined('PHOEBIUS_APP_VIEWS_ROOT')) {
	define('PHOEBIUS_APP_VIEWS_ROOT', PHOEBIUS_APP_ROOT . DIRECTORY_SEPARATOR . 'views');
}

define('PHOEBIUS_SHORT_PRODUCT_NAME', 'Phoebius v'.PHOEBIUS_VERSION);
define('PHOEBIUS_FULL_PRODUCT_NAME', 'Phoebius framework v'.PHOEBIUS_VERSION);


date_default_timezone_set('Europe/London');

define('PHOEBIUS_TYPE_EXTENSION', '.class.php');
define('PHOEBIUS_VIEW_EXTENSION', '.view.php');

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
	'Mvc/Exceptions',

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
	'App/Web/UrlRouting',
	'App/Web/UrlRouting/Exceptions',

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
	->register(E_ALL | E_STRICT, false, 'ExecutionContextException')
	->setException(E_USER_ERROR, 'CompilationContextException');


try {
	$result = is_dir(PHOEBIUS_TMP_ROOT);
	
	if (!$result) {
		mkdir(PHOEBIUS_TMP_ROOT, 0777, true);
	}
}
catch (ExecutionContextException $e) {
	die('Insufficient privileges to a temporary root (' . PHOEBIUS_TMP_ROOT . '): ' . $e->getMessage());
}
