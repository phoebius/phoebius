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
 * Aggregated by:
 *  - DBTable
 * @ingroup DB
 */
abstract class DBConstraint implements ISqlCastable
{
	/**
	 * @var string
	 */
	private $name;

	/**
	 * Returns the affected columns, if any
	 * @return array of {@link DBColumn}
	 */
	abstract function getIndexedColumns();

	/**
	 * @return string
	 */
	function getName()
	{
		return $this->name;
	}

	/**
	 * @return DBColumn
	 */
	function setName($name)
	{
		Assert::isScalar($name);

		$this->name = $name;

		return $this;
	}

	/**
	 * Casts an object to the SQL dialect string
	 * @return string
	 */
	function toDialectString(IDialect $dialect)
	{
		return $this->getHead($dialect) . ' ' . $this->getSql();
	}

	/**
	 * @return string
	 */
	protected function getHead(IDialect $dialect)
	{
		return 'CONSTRAINT '.$dialect->quoteIdentifier($this->name);
	}
}

?>