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
 * Represents a SQL join with a complex condition
 * @ingroup SelectQueryHelpers
 * @internal
 */
class SqlConditionalJoin extends SqlJoin
{
	/**
	 * @var IDalExpression
	 */
	private $condition;

	/**
	 * @param string $tableName
	 * @param SqlJoinMethod $joinMethod
	 * @param IDalExpression $condition
	 */
	function __construct($tableName, SqlJoinMethod $joinMethod, IDalExpression $condition)
	{
		parent::__construct($tableName, $joinMethod);
		$this->condition = $condition;
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
		$compiledSlices[] = 'ON';
		$compiledSlices[] = '(';
		$compiledSlices[] = $this->condition->toDialectString($dialect);
		$compiledSlices[] = ')';

		$compiledString = join(' ', $compiledSlices);
		return $compiledString;
	}
}

?>