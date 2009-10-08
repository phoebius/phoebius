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
 * Thrown every time the PostgreSQL raises an error on the passed query
 * @ingroup DalExceptions
 */
final class PgSqlQueryException extends DBQueryException
{
	private $sqlState;

	/**
	 * @param ISqlQuery $query
	 * @param string $errormsg
	 * @param scalar $errorno
	 */
	function __construct(ISqlQuery $query, $errormsg, $errorno)
	{
		$this->sqlState = $errorno;

		parent::__construct($query, $errormsg, 0);
	}

	/**
	 * @return PgSqlError
	 */
	function getSystemMessage()
	{
		return PgSqlError::create($this->sqlState);
	}
}

?>