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
 * Represents table as a data source where the {@link SelectQuery} should be applied
 * @ingroup SelectQueryHelpers
 * @internal
 */
class TableSelectQuerySource extends SelectQuerySource
{
	/**
	 * @var string
	 */
	private $tableName;

	/**
	 * @param string $tableName
	 * @param string $alias
	 */
	function __construct($tableName, $tableAlias = null)
	{
		Assert::isScalar($tableName);

		$this->tableName = $tableName;
		$this->setAlias($tableAlias);
	}

	/**
	 * Casts the source itself to the sql-compatible string using the {@link IDialect}
	 * specified
	 * @return string
	 */
	protected function getCastedSourceExpression(IDialect $dialect)
	{
		return $dialect->quoteIdentifier($this->tableName);
	}
}

?>