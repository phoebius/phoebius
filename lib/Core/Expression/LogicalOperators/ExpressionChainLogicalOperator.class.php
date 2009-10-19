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
 * @ingroup Core_Expression_LogicalOperators
 */
final class ExpressionChainLogicalOperator extends LogicalOperator
{
	const CONDITION_AND = 'AND';
	const CONDITION_OR = 'OR';

	/**
	 * Creates an instance of {@link ExpressionChainLogicalOperator}
	 * @param string $id one of the class constants
	 * @return ExpressionChainLogicalOperator
	 */
	static function create($id)
	{
		return new self($id);
	}

	/**
	 * Creates an instance of {@link ExpressionChainLogicalOperator} with
	 * {@link ExpressionChainLogicalOperator::CONDITION_AND} chain joiner
	 * @return ExpressionChainLogicalOperator
	 */
	static function conditionAnd()
	{
		return self::create(self::CONDITION_AND);
	}

	/**
	 * Creates an instance of {@link ExpressionChainLogicalOperator} with
	 * {@link ExpressionChainLogicalOperator::CONDITION_OR} chain joiner
	 * @return ExpressionChainLogicalOperator
	 */
	static function conditionOr()
	{
		return self::create(self::CONDITION_OR);
	}

}

?>