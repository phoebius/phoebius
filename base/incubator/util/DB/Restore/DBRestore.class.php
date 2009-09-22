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

abstract class DBRestore
{
	/**
	 * @var DBConnector
	 */
	private $dbc;

	final function __construct(DBConnector $dbc)
	{
		$this->dbc = $dbc;
	}

	/**
	 * @return DBConnector
	 */
	final protected function getDBConnector()
	{
		return $this->dbc;
	}

	abstract function make($file);
}

?>