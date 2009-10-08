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

final class SysVLocker extends Locker
{
	private $locks = array();

	private function key2int($key)
	{
		return hexdec(substr(md5($key), 0, 8));
	}

	function acquire($key)
	{
		$result = false;

		try
		{
			if (!isset($this->locks[$key]))
			{
				$this->locks[$key] = sem_get($this->key2int($key), 1, 0660, true);
			}

			if ($this->locks[$key])
			{
				$result = sem_acquire($this->locks[$key]);
			}
			else
			{
				unset($this->locks[$key]);
				$result = false;
			}
		}
		catch (ExecutionContextException $e)
		{
			$result = false;
		}

		return $result;
	}

	function drop($key)
	{
		try
		{
			return sem_remove($this->locks[$key]);
		}
		catch (ExecutionContextException $e)
		{
			unset($this->locks[$key]); // already race-removed
			return false;
		}
	}

	function release($key)
	{
		try
		{
			return sem_release($this->locks[$key]);
		}
		catch (ExecutionContextException $e)
		{
			// acquired by another process
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

}

?>