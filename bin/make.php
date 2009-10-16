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

chdir(dirname(__FILE__));

require '../etc/app.init.php';

//////////////////////////////////////////////////////////////////////////////////////////////////

if (
		isset($_SERVER['TERM'])
		&& (
			   $_SERVER['TERM'] == 'xterm'
			|| $_SERVER['TERM'] == 'linux'
		)
	) {
	$console = new ConsoleOutput();

}
else {
	$console = new RawConsoleOutput();
}

//////////////////////////////////////////////////////////////////////////////////////////////////

if ($argc == 1) {
	exit('
Usage:
# make.php [switches] $app

where:
 - $app is the absolute path to the application with the unified directory structure
 - switches are not yet used
');
}

$appDir = realpath($argv[1]);

if (!$appDir) {
	exit ("Unknown path to the application {$argv[1]}");
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

$xmlSchema = $appDir . DIRECTORY_SEPARATOR . 'var' . DIRECTORY_SEPARATOR . 'domain.xml';
if (!file_exists($xmlSchema)) {
	exit ('$app/var/domain.xml not found at ' . $xmlSchema);
}

$domainBuilder = new XmlOrmDomainBuilder(new OrmDomain, $xmlSchema);

try {

	$ormDomain = $domainBuilder->build();

	$schemaDir = APP_ROOT . DIRECTORY_SEPARATOR . 'var' . DIRECTORY_SEPARATOR . 'db';
	$autoRoot = APP_ROOT . DIRECTORY_SEPARATOR . 'var' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'Domain';
	$publicRoot = APP_ROOT . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'Domain';

	foreach (array($schemaDir, $autoRoot, $publicRoot) as $_) {
		if (!is_dir($_)) {
			mkdir($_, 0777, true);
		}
	}

	$generator = new OrmGenerator($schemaDir, $autoRoot, $publicRoot);
	$generator->regeneratePublic();
	$generator->generate($ormDomain);
}
catch (Exception $e) {
	var_dump($e);
	exit ($e->getMessage() .' at ' . $e->getFile() . ':' . $e->getLine());
}


?>