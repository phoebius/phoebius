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

class PgSqlBackup extends DBBackup
{
	private $winDir = "Z:\\usr\\local\\pgsql\\bin";
	private $executable = "pg_dump";

	function make($storeStructure, $storeData)
	{
		$cmd = ShellCommand::create($this->executable, $this->winDir);
		$dbc = $this->getDBConnector();

		if ( $storeData && !$storeStructure )
		{
			$cmd->addArg(ShellArg::create("--data-only"));
		}
		if ( $storeStructure && !$storeData )
		{
			$cmd->addArg(ShellArg::create("--schema-only"));
		}

		$cmd->addArg(ShellArg::create("--clean"));

		//--column-inserts
		//$cmd->AddArg("-D");

		//target file
		$cmd->addArg(ShellArg::create("-f", $this->getTarget()));

		//--format=format
		$cmd->addArg(ShellArg::create('-F', 'p'));

		$cmd->addArg(ShellArg::create("--no-owner"));
		$cmd->addArg(ShellArg::create("--no-privileges"));

		//connection settings
		if ($dbc->getHost())
		{
			$cmd->addArg(ShellArg::create('-h', $dbc->getHost()));
		}

		//dbname
		$cmd->addArg(ShellArg::create()->setValue($dbc->getDbName()));

		if (substr(PHP_OS, 0, 3) == 'WIN')
		{
			putenv('PGUSER=' . $dbc->getUser());
			putenv('PGPASSWORD=' . $dbc->getPassword());
		}
		else
		{
			//linux has a better utils to call executables within the changed env
			$env = new ShellCommand('env');
			$env->addArg(ShellArg::create()->setValue('PGUSER=' . $dbc->getUser()));
			$env->addArg(ShellArg::create()->setValue('PGPASSWORD=' . $dbc->getPassword()));
			$env->addArg(ShellArg::create($cmd->getFullCommand()));

			$cmd = $env;
		}

		$cmd->execute(
			PathResolver::getInstance()
				->getLogDir($this, $dbc->getDriver()->getName() . "." . $dbc->getDbName() . '.log')
		);
	}
}

?>