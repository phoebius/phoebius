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

Autoloader::getInstance()->clearCache();

//////////////////////////////////////////////////////////////////////////////////////////////////

function help()
{
	echo <<<EOT
Usage:
# make.php [options] [domain-schema.xml]

Possible options:
 --app-dir=<dir>		path to the directory where unified application FS resides.
 						If not specified, the current directory is used.

 --regenerate-public	forces make to regenerate public files

 --public=<dir>			not yet implemented

 --auto=<dir>			not yet implemented

 --schema=<file>		not yet implemented

 --dry-run				not yet implemented

EOT;
}

function stop($message = null)
{
	if ($message) {
		echo $message, PHP_EOL, PHP_EOL;
	}

	help();

	exit(1);
}

$appDir = getcwd();
$regeneratePublic = false;
$dryRun = false;

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
			case '--app-dir': {
				$appDir = realpath($v);
				break;
			}

			case '--regenerate-public': {
				$regeneratePublic = true;
				break;
			}

			case '--dry-run': {
				$dryRun = true;
				break;
			}

			default: {
				stop('Unknown option '. $k);
			}
		}
	}
}

if (!is_dir($appDir)) {
	stop ("Unknown path to the application {$appDir}");
}

$xmlSchema = end($args);
if ($xmlSchema && $xmlSchema{0} != '-') {
	$prefixes = array(
		'',
		$appDir . '/',
		getcwd() . '/',
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
	stop ('XML Schema of domain not found at ' . $xmlSchema);
}

define('APP_ROOT', $appDir);

$applicationConfig = APP_ROOT . DIRECTORY_SEPARATOR . 'etc' . DIRECTORY_SEPARATOR . 'config.php';
if (file_exists($applicationConfig)) {
	include $applicationConfig;
}

$hostConfig = APP_ROOT . DIRECTORY_SEPARATOR . 'cfg' . DIRECTORY_SEPARATOR . APP_SLOT . DIRECTORY_SEPARATOR . 'config.php';
if (file_exists($hostConfig)) {
	include $hostConfig;
}

$domainBuilder = new XmlOrmDomainBuilder($xmlSchema);

try {

	$ormDomain = $domainBuilder->build();

	$schemaDir = APP_ROOT . DIRECTORY_SEPARATOR . 'var' . DIRECTORY_SEPARATOR . 'db';
	$autoRoot = APP_ROOT . DIRECTORY_SEPARATOR . 'var' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'Domain';
	$publicRoot = APP_ROOT . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'Domain';

	foreach (array($schemaDir, $autoRoot, $publicRoot) as $_) {
		if (!is_dir($_)) {
			mkdir($_, 0755, true);
		}
	}

	$generator = new OrmGenerator($schemaDir, $autoRoot, $publicRoot);
	if ($regeneratePublic) {
		$generator->regeneratePublic();
	}
	$generator->generate($ormDomain);
}
catch (Exception $e) {
	stop ($e->getMessage() .' at ' . $e->getFile() . ':' . $e->getLine());
}

echo 'Done', PHP_EOL;

?>