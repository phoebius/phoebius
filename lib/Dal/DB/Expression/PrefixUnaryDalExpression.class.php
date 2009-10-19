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
 * Represents a unary prefix expression
 * @ingroup Dal_DB_Expression
 */
class PrefixUnaryDalExpression implements IDalExpression
{
	/**
	 * @var ISqlCastable
	 */
	private $subject;

	/**
	 * @var PrefixUnaryLogicalOperator
	 */
	private $logic;

	/**
	 * @param PrefixUnaryLogicalOperator $logic
	 * @param ISqlValueExpression $subject probably, {@link SqlColumn}, but can be either
	 * 	{@link SelectQuery} or any other sql expression
	 */
	function __construct(PrefixUnaryExpression $expression)
	{
		$this->subject = $expression->getSubject();
		$this->logic = $expression->getLogicalOperator();
	}

	/**
	 * Casts an object to the SQL dialect string
	 * @return string
	 */
	function toDialectString(IDialect $dialect)
	{
		$compiledSlices = array();

		$compiledSlices[] = $this->logic->toDialectString($dialect);
		$compiledSlices[] = '(';
		$compiledSlices[] = $this->subject->toDialectString($dialect);
		$compiledSlices[] = ')';

		$compiledString = join(' ', $compiledSlices);

		return $compiledString;
	}
}

?>