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
 * Thrown in case the INSERT/UPDATE/DELETE query violates the database constraints (foreign
 * keys, unique indicies, etc)
 * @ingroup DalExceptions
 */
class UniqueViolationException extends DBQueryException
{
	/**
	 * @param ISqlQuery $query
	 * @param string $errormsg
	 */
	function __construct(ISqlQuery $query, $errormsg)
	{
		parent::__construct($query, $errormsg, 0);
	}
}

?>