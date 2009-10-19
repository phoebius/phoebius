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
 * Represents table as a data source where the {@link SelectQuery} should be applied
 * @ingroup Dal_DB_Query
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