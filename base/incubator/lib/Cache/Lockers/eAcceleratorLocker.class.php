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

final class eAcceleratorLocker extends Locker
{
	function drop($key)
	{
		$this->release($key);
	}

	function acquire($key)
	{
		return eaccelerator_lock($key);
	}

	function release($key)
	{
		return eaccelerator_unlock($key);
	}

	function dropAll()
	{
		//automated
	}
}

?>