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
 * Represents a prefix unary logical operator
 * @ingroup Core_Expression_LogicalOperators
 */
final class PrefixUnaryLogicalOperator extends LogicalOperator
{
	const NOT = 'NOT';
	const MINUS	= '-';

	/**
	 * Creates an instance of {@link PrefixUnaryLogicalOperator}
	 * @param string $id one of the class constants
	 * @return PrefixUnaryLogicalOperator
	 */
	static function create($id)
	{
		return new self($id);
	}

	/**
	 * Creates an instance of {@link PrefixUnaryLogicalOperator} with {@link PrefixUnaryLogicalOperator::NOT}
	 * value
	 * @return PrefixUnaryLogicalOperator
	 */
	static function not()
	{
		return self::create(self::NOT);
	}

	/**
	 * Creates an instance of {@link PrefixUnaryLogicalOperator} with {@link PrefixUnaryLogicalOperator::MINUS}
	 * value
	 * @return PrefixUnaryLogicalOperator
	 */
	static function minus()
	{
		return self::create(self::MINUS);
	}

}

?>