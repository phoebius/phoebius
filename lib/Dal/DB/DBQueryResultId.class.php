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
 * Represents a query result resource identifer
 * @ingroup Dal
 */
class DBQueryResultId
{
	/**
	 * @var DB
	 */
	private $db;

	/**
	 * @var resource
	 */
	private $resultId;

	/**
	 * @param DB $db
	 * @param resource $resultId
	 */
	function __construct(DB $db, $resultId)
	{
		Assert::isTrue(is_resource($resultId) || $resultId === true);

		$this->db = $db;
		$this->resultId = $resultId;
	}

	/**
	 * Checks whether the result id conforms the query that run the specified database hanle
	 * @return boolean
	 */
	function isValid(DB $db)
	{
		return $db === $this->db;
	}

	/**
	 * Returns the result id
	 */
	function getResultId()
	{
		return $this->resultId;
	}
}

?>