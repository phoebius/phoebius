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

abstract class Locker
{
	abstract protected function acquire($lockerId);
	abstract protected function drop($lockerId);
	abstract protected function dropAll();
	abstract protected function release($lockerId);

	function __destruct()
	{
		$this->dropAll();
	}
}

?>