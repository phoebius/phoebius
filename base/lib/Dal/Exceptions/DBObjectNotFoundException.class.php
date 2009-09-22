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
 * Thrown when the database object requested by a query cannot be fetched
 * @ingroup DalExceptions
 */
class DBObjectNotFoundException extends DataNotFoundException
{
	/**
	 * @var ISqlQuery
	 */
	private $query;

	/**
	 * @param string $tableName
	 * @param string $columnName
	 */
	function __construct(ISqlQuery $query)
	{
		parent::__construct();

		$this->query = $query;
	}

	/**
	 * Returns the query
	 * @return ISqlQuery
	 */
	function getQuery()
	{
		return $this->query;
	}
}

?>