<?php
/* ***********************************************************************************************
 *
 * Phoebius Framework
 *
 * **********************************************************************************************
 *
 * Copyright (c) 2009 phoebius.org
 *
 * This program is free software; you can redistribute it and/or modify it under the terms
 * of the GNU Lesser General Public License as published by the Free Software Foundation;
 * either version 3 of the License, or (at your option) any later version.
 *
 * You should have received a copy of the GNU Lesser General Public License along with
 * this program; if not, see <http://www.gnu.org/licenses/>.
 *
 ************************************************************************************************/

/**
 * Represents expression as a data source where the {@link SelectQuery} should be applied
 * @ingroup Dal_DB_Query
 * @internal
 */
class ComplexSelectQuerySource extends SelectQuerySource
{
	/**
	 * @var IExpression
	 */
	private $source;

	/**
	 * @param ISqlSelectable $tableName
	 * @param string $alias
	 */
	function __construct(ISqlSelectable $source, $alias = null)
	{
		$this->source = $source;
		$this->setAlias($alias);
	}

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