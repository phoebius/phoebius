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
	private $expression;

	/**
	 * @param string $tableName
	 * @param string|null
	 * @param SqlJoinMethod $joinMethod
	 * @param IDalExpression $condition
	 */
	function __construct($tableName, $alias, SqlJoinMethod $joinMethod, IDalExpression $expression)
	{
		parent::__construct($tableName, $alias, $joinMethod);

		$this->expression = $expression;
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
		$compiledSlices[] = 'ON';
		$compiledSlices[] = '(';
		$compiledSlices[] = $this->expression->toDialectString($dialect);
		$compiledSlices[] = ')';

		$compiledString = join(' ', $compiledSlices);
		return $compiledString;
	}
}

?>