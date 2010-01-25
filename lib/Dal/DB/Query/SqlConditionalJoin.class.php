<?php
/* ***********************************************************************************************
 *
 * Phoebius Framework
 *
 * **********************************************************************************************
 *
 * Copyright (c) 2009 Scand Ltd.
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
 * Represents a SelectQuerySource joiner which uses conditional expression for merging.
 *
 * This is the same for `JOIN USING`.
 *
 * @ingroup Dal_DB_Query
 * @aux
 */
final class SqlConditionalJoin extends SqlJoin
{
	/**
	 * @var IExpression
	 */
	private $condition;

	/**
	 * @param SelectQuerySource $source source to which join operation should be applied
	 * @param SqlJoinMethod $joinMethod method to use when performing join
	 * @param IExpression $condition condition to use when performing join
	 */
	function __construct(SelectQuerySource $source, SqlJoinMethod $joinMethod, IExpression $condition)
	{
		$this->condition = $condition;

		parent::__construct($source, $joinMethod);
	}

	function toDialectString(IDialect $dialect)
	{
		$compiledSlices = array();

		$compiledSlices[] = $this->getJoinMethod()->toDialectString($dialect);
		$compiledSlices[] = $this->getSource()->toDialectString($dialect);
		$compiledSlices[] = 'ON';
		$compiledSlices[] = '(';
		$compiledSlices[] = $this->condition->toDialectString($dialect);
		$compiledSlices[] = ')';

		$compiledString = join(' ', $compiledSlices);
		return $compiledString;
	}
}

?>