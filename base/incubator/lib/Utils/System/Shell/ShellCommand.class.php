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

final class ShellCommand implements IFactory
{
	private $executable;
	private $args = array();
	private $path;

	/**
	 * @return ShellCommand
	 */
	static function create($executable, $optionalWindowsPath = null)
	{
		return new self($executable, $optionalWindowsPath);
	}

	function __construct($executable, $optionalWindowsPath = null)
	{
		$this->executable = trim($executable);
		$chunks = explode(DIRECTORY_SEPARATOR, realpath($this->executable));
		if ( sizeof($chunks) > 1 )
		{
			$this->executable = array_pop($chunks);
			$this->setPath(join(DIRECTORY_SEPARATOR, $chunks));
		}
		else
		{
			$this->findPath($optionalWindowsPath);
		}
	}

	private function findPath($optionalWindowsPath = null)
	{
		//try to obtain the direct path usgin OS utils
		if ( substr(PHP_OS, 0, 3) != 'WIN' )
		{
			$whereIsResult = ShellCommand::create('whereis')->addArg($this->getExecutableName())->execute();
			$whereIsResult = end(explode(" ", $whereIsResult, 2));
			if ( $whereIsResult )
			{
				$chunks = explode(DIRECTORY_SEPARATOR, $whereIsResult);
				if ( $this->getExecutableName() == array_pop($chunks) )
				{
					$this->setPath(join(DIRECTORY_SEPARATOR, $chunks));
					return;
				}
			}
		}

		if ( $optionalWindowsPath )
		{
			$this->setPath($optionalWindowsPath);
		}
	}

	function getExecutableName()
	{
		return $this->executable;
	}

	function getPath()
	{
		return $this->path;
	}

	/**
	 * @return ShellCommand
	 */
	function setPath($path)
	{
		$this->path = realpath($path);
		if ( $this->path != DIRECTORY_SEPARATOR )
		{
			$this->path = ($this->path) . DIRECTORY_SEPARATOR;
		}

		return $this;
	}

	/**
	 * @return ShellCommand
	 */
	function addArg(ShellArg $arg)
	{
		$this->args[] = $arg;

		return $this;
	}

	/**
	 * @return ShellCommand
	 */
	function dropArg($argName)
	{
		foreach ( $this->args as $key => $arg )
		{
			if ( $arg->getName() == $argName )
			{
				unset($this->args[$key]);
			}
		}

		return $this;
	}

	function isSetArg($argName)
	{
		foreach ( $this->args as $arg )
		{
			if ( $arg->getName() == $argName )
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * Path + executable + args
	 */
	function getFullCommand()
	{
		$command = $this->path . $this->executable;
		if ( !empty($this->args) )
		{
			$command .= " " . $this->getGluedArgs();
		}

		return $command;
	}

	function execute($logPath = null)
	{
		$command = $this->getFullCommand();
		$out = shell_exec($command);

		if ( ! $logPath )
		{
			$logPath = PathResolver::getInstance()->getTmpDir($this, $this->getExecutableName() . '.txt');
		}
		file_put_contents($logPath, $command . PHP_EOL . $out);

		return $out;
	}

	private function getGluedArgs()
	{
		$eax = array();
		foreach ( $this->args as $arg )
		{
			$eax[] = $arg->toString();
		}

		return join(" ", $eax);
	}
}

?>