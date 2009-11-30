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
 * Represents a logical operator used in expression chaining
 * @ingroup Dal_Expression_LogicalOperators
 */
final class ExpressionChainLogicalOperator extends LogicalOperator
{
	const CONDITION_AND = 'AND';
	const CONDITION_OR = 'OR';

	/**
	 * Disjunction operator
	 * @return ExpressionChainLogicalOperator
	 */
	static function conditionAnd()
	{
		return new self(self::CONDITION_AND);
	}

	/**
	 * Conjunction operator
	 * @return ExpressionChainLogicalOperator
	 */
	static function conditionOr()
	{
		return new self(self::CONDITION_OR);
	}

}

?>