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

/**
 * Thrown when the connection to DB fails
 * @ingroup DalExceptions
 */
class DBConnectionException extends DBException
{
	/**
	 * @var DB
	 */
	private $db;

	/**
	 * @param DB $dbHandle DB with failed connection parameters
	 * @param string $errorMessage actual error string
	 */
	function __construct(DB $dbHandle, $errorMessage)
	{
		parent::__construct($errorMessage);

		$this->db = $dbHandle;
	}

	/**
	 * Returns the db handle with connection parameters that failed
	 * @return DB
	 */
	function getDB()
	{
		return $this->db;
	}
}

?>