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
 * Represents the IN expression used in query logic
 * @ingroup Dal_DB_Expression
 */
class InSetDalExpression implements IDalExpression
{
	/**
	 * @var SqlColumn
	 */
	private $field;

	/**
	 * @var ISqlCastable
	 */
	private $set;

	/**
	 * @var InSetLogicalOperator
	 */
	private $logic;

	function __construct(InSetExpression $expression)
	{
		$this->field = $expression->getSubject();
		$this->set = $expression->getSet();
		$this->logic = $expression->getLogicalOperator();
	}

	/**
	 * Casts an object to the SQL dialect string
	 * @return string
	 */
	function toDialectString(IDialect $dialect)
	{
		$compiledSlices = array();

		$compiledSlices[] = '(';
		$compiledSlices[] = $this->field->toDialectString($dialect);
		$compiledSlices[] = ')';
		$compiledSlices[] = $this->logic->toDialectString($dialect);
		$compiledSlices[] = '(';
		$compiledSlices[] = $this->set->toDialectString($dialect);
		$compiledSlices[] = ')';

		$compiledString = join(' ', $compiledSlices);

		return $compiledString;
	}
}

?>