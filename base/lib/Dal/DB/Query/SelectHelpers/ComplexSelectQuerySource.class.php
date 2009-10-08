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
 * Represents expression as a data source where the {@link SelectQuery} should be applied
 * @ingroup SelectQueryHelpers
 * @internal
 */
class ComplexSelectQuerySource extends SelectQuerySource
{
	/**
	 * @var IDalExpression
	 */
	private $source;

	/**
	 * @param ISelectQuerySource $tableName
	 * @param string $alias
	 */
	function __construct(ISelectQuerySource $source, $alias = null)
	{
		$this->source = $source;
		$this->setAlias($alias);
	}

	/**
	 * Casts the source itself to the sql-compatible string using the {@link IDialect}
	 * specified
	 * @return string
	 */
	protected function getCastedSourceExpression(IDialect $dialect)
	{
		$sourceSlices = array();

		$sourceSlices[] = '(';
		$sourceSlices[] = $this->source->toDialectString($dialect);
		$sourceSlices[] = ')';

		$compiledSourceString = join(' ', $sourceSlices);

		return $compiledSourceString;
	}
}

?>