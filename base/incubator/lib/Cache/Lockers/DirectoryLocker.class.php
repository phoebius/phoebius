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

final class DirectoryLocker extends Locker
{
	private $locks = array();
	private $directory;

	function __construct($customDirectory = null)
	{
		if (!$customDirectory)
		{
			$customDirectory = PathResolver::getInstance()->getTmpDir($this);
		}
		$this->directory = $customDirectory;
	}

	function acquire($key)
	{
		Assert::isScalar($key);

		$mseconds = 0;
		$result = false;

		while ($mseconds < 1)
		{
			try
			{
				$result = mkdir($this->getDirectoryByKey($key), 0700, false);
				$this->locks[$key] = true;
				break;
			}
			catch (ExecutionContextException $e)
			{
				// still exist
				$mseconds += 200;
				usleep(200);
			}
		}

		return $result;
	}

	function drop($key)
	{
		try
		{
			rmdir($this->getDirectoryByKey($key));
			unset($this->locks[$key]);
			return true;
		}
		catch (ExecutionContextException $e)
		{
			return false;
		}
	}

	function dropAll()
	{
		foreach(array_keys($this->locks) as $key)
		{
			$this->drop($key);
		}
	}

	function release($key)
	{
		return $this->drop($key);
	}

	private function getDirectoryByKey($key)
	{
		return $this->directory . DIRECTORY_SEPARATOR . sha1($key);
	}

}

?>