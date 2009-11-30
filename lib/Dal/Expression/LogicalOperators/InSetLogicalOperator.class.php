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
 * Represents a logical operator for InSetExpression to search fields in set of values
 * @ingroup Dal_Expression_LogicalOperators
 */
final class InSetLogicalOperator extends LogicalOperator
{
	const IN = 'IN';
	const NOT_IN = 'NOT IN';

	/**
	 * Creates an instance of {@link InSetLogicalOperator} with
	 * {@link InSetLogicalOperator::IN} value
	 * @return InSetLogicalOperator
	 */
	static function in()
	{
		return new self (self::IN);
	}

	/**
	 * Creates an instance of {@link InSetLogicalOperator} with
	 * {@link InSetLogicalOperator::NOT_IN} value
	 * @return InSetLogicalOperator
	 */
	static function notIn()
	{
		return new self (self::NOT_IN);
	}
}

?>