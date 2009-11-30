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
 * Represents a SelectQuerySource joiner which joins multiple sources by the set of same fiels
 *
 * This is the same for `JOIN ON `.
 *
 * @ingroup Dal_DB_Query
 * @aux
 */
final class SqlSimpleJoin extends SqlJoin
{
	/**
	 * @var SqlFieldArray
	 */
	private $identicalColumns;

	/**
	 * @param SelectQuerySource $source source to which join operation should be applied
	 * @param SqlJoinMethod $joinMethod method to use when performing join
	 * @param SqlFieldArray $identicalColumns set of column names that should be used in joining
	 */
	function __construct(SelectQuerySource $source, SqlJoinMethod $joinMethod, SqlFieldArray $identicalColumns)
	{
		$this->identicalColumns = $identicalColumns;

		parent::__construct($source, $joinMethod);
	}

	function toDialectString(IDialect $dialect)
	{
		$compiledSlices = array();

		$compiledSlices[] = $this->getJoinMethod()->toDialectString($dialect);
		$compiledSlices[] = $this->getSource()->toDialectString($dialect);
		$compiledSlices[] = 'USING';
		$compiledSlices[] = '(';
		$compiledSlices[] = $this->identicalColumns->toDialectString($dialect);
		$compiledSlices[] = ')';

		$compiledString = join(' ', $compiledSlices);
		return $compiledString;
	}
}

?>