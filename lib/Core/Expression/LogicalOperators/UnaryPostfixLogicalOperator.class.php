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
 * Represents an operator used in unary prefix expression
 * @ingroup Core_Expression_LogicalOperators
 */
final class UnaryPostfixLogicalOperator extends LogicalOperator
{
	const IS_NULL = 'IS NULL';
	const IS_NOT_NULL = 'IS NOT NULL';

	const IS_TRUE = 'IS TRUE';
	const IS_FALSE = 'IS FALSE';

	/**
	 * Creates an instance of {@link UnaryPostfixLogicalOperator}
	 * @param string $id one of the class constants
	 * @return UnaryPostfixLogicalOperator
	 */
	static function create($id)
	{
		return new self($id);
	}

	/**
	 * Creates an instance of {@link UnaryPostfixLogicalOperator} with
	 * {@link UnaryPostfixLogicalOperator::IS_NULL} value
	 * @return UnaryPostfixLogicalOperator
	 */
	static function isNull()
	{
		return self::create(self::IS_NULL);
	}

	/**
	 * Creates an instance of {@link UnaryPostfixLogicalOperator} with
	 * {@link UnaryPostfixLogicalOperator::IS_NOT_NULL} value
	 * @return UnaryPostfixLogicalOperator
	 */
	static function isNotNull()
	{
		return self::create(self::IS_NOT_NULL);
	}

	/**
	 * Creates an instance of {@link UnaryPostfixLogicalOperator} with
	 * {@link UnaryPostfixLogicalOperator::IS_TRUE} value
	 * @return UnaryPostfixLogicalOperator
	 */
	static function isTrue()
	{
		return self::create(self::IS_TRUE);
	}

	/**
	 * Creates an instance of {@link UnaryPostfixLogicalOperator} with
	 * {@link UnaryPostfixLogicalOperator::IS_FALSE} value
	 * @return UnaryPostfixLogicalOperator
	 */
	static function isFalse()
	{
		return self::create(self::IS_FALSE);
	}

}

?>