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
 * Represents an range expression
 * @ingroup Dal_DB_Expression
 */
class BetweenRangeDalExpression implements IDalExpression
{
	/**
	 * @var SqlColumn
	 */
	private $field;

	/**
	 * @var ISqlCastable
	 */
	private $from;

	/**
	 * @var ISqlCastable
	 */
	private $to;

	function __construct(BetweenRangeExpression $expression)
	{
		$this->field = $expression->getSubject();
		$this->to = $expression->getTo();
		$this->from = $expression->getFrom();
	}

	/**
	 * Casts an object to the SQL dialect string
	 * @return string
	 */
	function toDialectString(IDialect $dialect)
	{
		$compiledSlices = array();

		$compiledSlices[] = $this->field->toDialectString($dialect);
		$compiledSlices[] = 'BETWEEN';
		$compiledSlices[] = '(';
		$compiledSlices[] = $this->from->toDialectString($dialect);
		$compiledSlices[] = ')';
		$compiledSlices[] = 'AND';
		$compiledSlices[] = '(';
		$compiledSlices[] =  $this->to->toDialectString($dialect);
		$compiledSlices[] = ')';

		$compiledString = join(' ', $compiledSlices);

		return $compiledString;
	}
}

?>