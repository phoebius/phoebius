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

abstract class DBBackup
{
	/**
	 * @var DBConnector
	 */
	private $dbc = null;

	/**
	 * @var string
	 */
	private $target;

	final function __construct(DBConnector $dbc)
	{
		$this->dbc = $dbc;
	}

	/**
	 * @return DBBackup
	 */
	function setTarget($target)
	{
		$this->target = $target;

		return $this;
	}

	function getTarget()
	{
		return $this->target;
	}

	/**
	 * @return DBConnector
	 */
	final protected function getDBConnector()
	{
		return $this->dbc;
	}

	abstract function make($storeStructure, $storeData);
}

?>