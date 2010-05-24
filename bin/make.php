#!/usr/bin/php
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

require dirname(__FILE__).'/../etc/app.init.php';

//////////////////////////////////////////////////////////////////////////////////////////////////

function message($message)
{
	echo $message, PHP_EOL;
}

function help()
{
	echo <<<EOT

Usage:

  $ make.php [options] [domain-schema.xml]

If domain-schema.xml is not specified then make.php uses \$app/var/domain.xml.
\$app is treated as the current directory, if --app-dir option is not set.


General options:

  --app-dir=<dir>        an \$app, a path to the directory where an application resides. Can be
                         either an absolute path or a path relative to the current directory.

                         Application directory should contain at least \$app/etc/config.php.
                         If not specified, the current directory is used.

  --host-config=<name>   name of the host configuration resides at \$app/cfg/<name>/config.php.

  --dry-run              modify nothing, show the results only. Currently not implemented.

  --help, -h             show this help.


Code generator options:

  --code                 generate and ORM over the defined schema: an auxiliary PHP classes
                         and business-entities.

  --regenerate-public    regenerate public files if already generated. This forces a --code option
                         to be switched on automatically.

  --public-dir=<dir>     write generated public class' files to <dir>. Default is lib/Domain.
                         Path is treated as relative to the application directory (\$app).

  --auto-dir=<dir>       write generated internal class' files to <dir>. Default is var/lib/Domain.
                         Path is treated as relative to the application directory (\$app).


Database schema generator options:

  --schema               generate database schema. You should set the name of the database config
                         either in domain-schema.xml (<domain db-schema="<name>">) or
                         using the --db option explicitly.

  --db=<name>            use this database to generate schema (should be added to DBPool). This
                         can be set implicitly in domain-schema.xml (<domain db-schema="<name>">).
                         This forces a --schema option to be switched on automatically.

  --import               import schema to the database. Currently not implemented.

  --schema-file=<file>   write database schema to <file>.
                         Default is var/db/<db_driver>-<domain-schema_name>.sql.
                         Path is treated as relative to the application directory (\$app).
                         This forces a --schema option to be switched on automatically.

EOT;
}

function stop($message = null)
{
	if ($message) {
		message($message);
	}

	echo <<<EOT

Use

  $ {$GLOBALS['argv'][0]} --help

for more information.


EOT;

	//help();

	exit(1);
}

$appDir = getcwd();
$hostConfig = null;
$dryRun = false;

$code = false;
$regeneratePublic = false;
$publicDir = 'lib/Domain';
$autoDir = 'var/lib/Domain';

$schema = false;
$db = null;
$import = false;
$schemaFile = null;
$dbObject = null;


$args = $argv;
array_shift($args);
foreach ($args as $arg) {
	if ($arg{0} == '-') {
		if (strpos($arg, '=')) {
			list ($k, $v) = explode('=', $arg, 2);
		}
		else {
			$k = $arg;
			$v = null;
		}

		switch ($k) {

			//
			// general
			//

			case '--app-dir': {
				$appDir = realpath($v);
				break;
			}

			case '--host-config': {
				$hostConfig = $v;
				break;
			}

			case '--dry-run': {
				$dryRun = true;
				break;
			}

			case '--help':
			case '-h': {
				help();

				exit;
			}

			//
			// code generator
			//

			case '--code': {
				$code = true;
				break;
			}

			case '--regenerate-public': {
				$code = true;
				$regeneratePublic = true;
				break;
			}

			case '--public-dir': {
				$publicDir = $v;
				break;
			}

			case '--auto-dir': {
				$autoDir = $v;
			}

			//
			// schema generator
			//

			case '--schema': {
				$schema = true;
				break;
			}

			case '--db': {
				$schema = true;
				$db = $v;
				break;
			}

			case '--import': {
				$import = true;
				break;
			}

			case '--schema-file': {
				$schema = true;
				$schemaFile = $v;
				break;
			}

			default: {
				stop('Unknown option '. $k);
			}
		}
	}
}

if (!$code && !$schema) {
	stop('Nothing to do: neither --code nor --schema is set');
}

if (!is_dir($appDir)) {
	stop ("Unknown path to the application: {$appDir}");
}

chdir($appDir);

foreach (array('publicDir', 'autoDir') as $dir) {
	$$dir = $appDir . '/' . $$dir;
	if (!is_dir($$dir)) {
		mkdir($$dir, 0755, true);
	}
}

$xmlSchema = end($args);
if ($argv > 1 && $xmlSchema && $xmlSchema{0} != '-') {
	$prefixes = array(
		'',
		$appDir . '/',
	);

	foreach ($prefixes as $prefix) {
		if (file_exists($prefix . $xmlSchema) && is_file($prefix . $xmlSchema)) {
			$xmlSchema = realpath($prefix . $xmlSchema);
			break;
		}
	}
}
else {
	$xmlSchema = $appDir . '/var/domain.xml';
}

if (!file_exists($xmlSchema)) {
	stop ('Domain schema not found (' . $xmlSchema . ' does not exist)');
}

define('APP_ROOT', $appDir);

$applicationConfig = APP_ROOT . DIRECTORY_SEPARATOR . 'etc' . DIRECTORY_SEPARATOR . 'config.php';
if (file_exists($applicationConfig)) {
	include $applicationConfig;
}

if ($hostConfig) {
	$hostConfigFile = APP_ROOT . DIRECTORY_SEPARATOR . 'cfg' . DIRECTORY_SEPARATOR . $hostConfig . DIRECTORY_SEPARATOR . 'config.php';
	if (file_exists($hostConfigFile)) {
		include $hostConfigFile;
	}
	else {
		stop ("`{$hostConfig}` host config does not exist");
	}
}

if (!defined('APP_SLOT_CONFIGURATION')) {
	define ('APP_SLOT_CONFIGURATION', SLOT_PRESET_PRODUCTION);
}

Autoloader::getInstance()->clearCache();

$domainBuilder = new XmlOrmDomainBuilder($xmlSchema);

try {

	$ormDomain = $domainBuilder->build();

	if ($code) {
		$generator = new OrmGenerator($autoDir, $publicDir);

		if ($regeneratePublic) {
			$generator->regeneratePublic();
		}

		$generator->generate($ormDomain);
	}

	if ($schema) {
		if (!$db && $ormDomain->getDbSchema()) {
			$db = $ormDomain->getDbSchema();
		}

		if ($db) {
			try {
				$dbObject = DBPool::get($db);
			}
			catch (ArgumentException $e) {
				stop ("Unknown database reference: $db");
			}
		}

		if ($schemaFile) {
			$schemaFile = $appDir . DIRECTORY_SEPARATOR . $schemaFile;
		}
		else {
			$schemaFile =
				$appDir
				. DIRECTORY_SEPARATOR . 'var'
				. DIRECTORY_SEPARATOR . 'db'
				. DIRECTORY_SEPARATOR
				. strtolower($dbObject->getDialect()->getDBDriver()->getValue())
				. '-' . pathinfo($xmlSchema, PATHINFO_FILENAME) . '.sql';

			$dir = dirname($schemaFile);
			if (!is_dir($dir)) {
				mkdir($dir, 0755, true);
			}
		}

		$schemaBuilder = new DBSchemaBuilder($ormDomain);

		$schemaConstructor = new SqlSchemaConstructor($schemaBuilder->build());

		$schemaConstructor
			->make(
				new FileWriteStream($schemaFile),
				$dbObject->getDialect()
			);
	}
}
catch (Exception $e) {
	stop ($e->getMessage() .' at ' . $e->getFile() . ':' . $e->getLine());
}

echo 'Done', PHP_EOL;

?>