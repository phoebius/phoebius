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

$logger = new ConsoleExecutionRecorder($console);

//////////////////////////////////////////////////////////////////////////////////////////////////

if ($argc == 1) {
	$logger->putMsg('
Usage:
# make.php [switches] $app

where:
 - $app is the absolute path to the application with the unified directory structure
 - switches are not yet used
');

	exit(1);
}

$appDir = realpath($argv[1]);

if (!$appDir) {
	$logger->putErrorLine("Unknown path to the application {$argv[0]}");
	exit;
}

define('APP_ROOT', $appDir);

$applicationConfig = APP_ROOT . DIRECTORY_SEPARATOR . 'etc' . DIRECTORY_SEPARATOR . 'config.php';
if (file_exists($applicationConfig)) {
	$logger->putMsgLine('Including the application config');
	include $applicationConfig;
}

$hostConfig = APP_ROOT . DIRECTORY_SEPARATOR . 'cfg' . DIRECTORY_SEPARATOR . APP_SLOT . DIRECTORY_SEPARATOR . 'config.php';
if (file_exists($hostConfig)) {
	$logger->putMsgLine('Including the host config');
	include $hostConfig;
}

$xmlSchema = $appDir . DIRECTORY_SEPARATOR . 'var' . DIRECTORY_SEPARATOR . 'domain.xml';
if (!file_exists($xmlSchema)) {
	$logger->putErrorLine('$app/var/domain.xml not found at ' . $xmlSchema);
	exit (1);
}

$xmlOrmDomainImporter = new XmlOrmDomainImporter($logger, $xmlSchema);

try {

	$ormDomain = $xmlOrmDomainImporter->import(
		new OrmDomain()
	);

	$schemaDir = APP_ROOT . DIRECTORY_SEPARATOR . 'var' . DIRECTORY_SEPARATOR . 'db';
	$autoRoot = APP_ROOT . DIRECTORY_SEPARATOR . 'var' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'Domain';
	$publicRoot = APP_ROOT . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'Domain';

	foreach (array($schemaDir, $autoRoot, $publicRoot) as $_) {
		if (!is_dir($_)) {
			mkdir($_, 0777, true);
		}
	}

	$generator = new OrmGenerator($schemaDir, $autoRoot, $publicRoot);

	$dbSchema = DBSchemaImporter::create()->import($ormDomain, new DBSchema());

	$generator->generate($ormDomain);
}
catch (Exception $e) {
	$logger->putErrorLine($e->getMessage());
	exit (1);
}


?>