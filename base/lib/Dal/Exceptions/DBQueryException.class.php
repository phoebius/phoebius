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
 * Thrown every time the database raises an error on the passed query
 * @ingroup DalExceptions
 */
class DBQueryException extends DBException
{
	/**
	 * @var string
	 */
	private $query;

	/**
	 * @param ISqlQuery $query
	 * @param string $errormsg
	 * @param integer $errorno
	 */
	function __construct(ISqlQuery $query, $errormsg, $errorno)
	{
		Assert::isScalar($errormsg);
		Assert::isNumeric($errorno);

		parent::__construct($errormsg, $errorno);
		$this->query = $query;
	}

	/**
	 * @return ISqlQuery
	 */
	function getQuery()
	{
		return $this->query;
	}
}

?>