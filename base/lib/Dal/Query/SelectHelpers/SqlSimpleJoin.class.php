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
 * Represents a SQL join with simple condition where the joining tables has the identical names
 * @ingroup SelectQueryHelpers
 * @internal
 */
class SqlSimpleJoin extends SqlJoin
{
	/**
	 * @var SqlFieldList
	 */
	private $identicalColumns;

	/**
	 * @param string $tableName
	 * @param string|null
	 * @param SqlJoinMethod $joinMethod
	 * @param SqlFieldList $identicalColumns set of column names that should be used in joining
	 */
	function __construct($tableName, $alias, SqlJoinMethod $joinMethod, SqlFieldList $identicalColumns)
	{
		$this->identicalColumns = $identicalColumns;

		parent::__construct($tableName, $alias, $joinMethod);
	}

	/**
	 * Casts an object to the SQL dialect string
	 * @return string
	 */
	function toDialectString(IDialect $dialect)
	{
		$compiledSlices = array();

		$compiledSlices[] = $this->getJoinMethod()->toDialectString($dialect);
		$compiledSlices[] = $dialect->quoteIdentifier($this->getTableName());
		if (($alias = $this->getTableAlias())) {
			$compiledSlices[] = $dialect->quoteIdentifier($alias);
		}
		$compiledSlices[] = 'USING';
		$compiledSlices[] = '(';
		$compiledSlices[] = $this->identicalColumns->toDialectString($dialect);
		$compiledSlices[] = ')';

		$compiledString = join(' ', $compiledSlices);
		return $compiledString;
	}
}

?>