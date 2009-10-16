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
 * @ingroup Sql
 */
class SqlIdentifier implements ISqlCastable
{
	private $id;

	function __construct($id)
	{
		$this->setId($id);
	}

	/**
	 * @return SqlIdentifier
	 */
	function setId($id)
	{
		Assert::isScalar($id);

		$this->id = $id;

		return $this;
	}

	/**
	 * @return string
	 */
	function getId()
	{
		return $this->id;
	}

	/**
	 * Casts an object to the SQL dialect string
	 * @return string
	 */
	function toDialectString(IDialect $dialect)
	{
		return $dialect->quoteIdentifier($this->id);
	}
}

?>