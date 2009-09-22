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

class PgSqlRestore extends DBRestore
{
	private $winDir = "Z:\\usr\\local\\pgsql\\bin";
	private $executable = "psql";

	function make($file)
	{
		$cmd = new ShellCommand($this->executable, $this->winDir);
		$dbc = $this->getDBConnector();

		if ($dbc->getHost())
		{
			$cmd->AddArg("-h", $dbc->getHost());
		}

		$cmd->addArg(new ShellArg($dbc->getDbName()));
		$cmd->addArg(new ShellArg(' < ', $file));

		//we are to use local envvars because our magic dances with proc_open and writing
		//the password directly to the pipe failed
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
				->getLogDir($this, $dbc->getDriver()->getName() . "." . $dbc->getName() . '.log')
		);
	}
}

?>