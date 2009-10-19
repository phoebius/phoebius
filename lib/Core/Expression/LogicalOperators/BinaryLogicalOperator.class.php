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
 * Represents a logical operator used in {@link BinaryExpression}
 * @ingroup Core_Expression_LogicalOperators
 */
final class BinaryLogicalOperator extends LogicalOperator
{
	const EQUALS = '=';
	const NOT_EQUALS = '!=';

	const EXPRESSION_AND = 'AND';
	const EXPRESSION_OR = 'OR';

	const GREATER_THAN = '>';
	const GREATER_OR_EQUALS = '>=';

	const LOWER_THAN = '<';
	const LOWER_OR_EQUALS = '<=';

	const LIKE = 'LIKE';
	const NOT_LIKE = 'NOT LIKE';
	const ILIKE = 'ILIKE';
	const NOT_ILIKE = 'NOT_ILIKE';

	const SIMILAR_TO = 'SIMILAR TO';
	const NOT_SIMILAR_TO = 'NOT SIMILAR TO';

	const ADD = '+';
	const SUBSTRACT = '-';
	const MULTIPLY = '*';
	const DIVIDE = '/';


	/**
	 * Creates an instance of {@link BinaryLogicalOperator}
	 * @param string $id one of the class constants
	 * @return BinaryLogicalOperatorLogicalOperator
	 */
	static function create($id)
	{
		return new self($id);
	}

	/**
	 * Creates an instance of {@link BinaryLogicalOperator} with {@link BinaryExpression::EQUALS} value
	 * @return BinaryLogicalOperator
	 */
	static function equals()
	{
		return self::create(self::EQUALS);
	}

	/**
	 * Creates an instance of {@link BinaryLogicalOperator} with {@link BinaryExpression::NOT_EQUALS}
	 * value
	 * @return BinaryLogicalOperator
	 */
	static function notEquals()
	{
		return self::create(self::NOT_EQUALS);
	}

	/**
	 * Creates an instance of {@link BinaryLogicalOperator} with {@link BinaryExpression::EXPRESSION_AND}
	 * value
	 * @return BinaryLogicalOperator
	 */
	static function expAnd()
	{
		return self::create(self::EXPRESSION_AND);
	}

	/**
	 * Creates an instance of {@link BinaryLogicalOperator} with {@link BinaryExpression::EXPRESSION_OR}
	 * value
	 * @return BinaryLogicalOperator
	 */
	static function expOr()
	{
		return self::create(self::EXPRESSION_OR);
	}

	/**
	 * Creates an instance of {@link BinaryLogicalOperator} with {@link BinaryExpression::GREATER_THAN}
	 * value
	 * @return BinaryLogicalOperator
	 */
	static function greaterThan()
	{
		return self::create(self::GREATER_THAN);
	}

	/**
	 * Creates an instance of {@link BinaryLogicalOperator} with
	 * {@link BinaryExpression::GREATER_THAN_OR_EQUALS} value
	 * @return BinaryLogicalOperator
	 */
	static function greaterOrEquals()
	{
		return self::create(self::GREATER_OR_EQUALS);
	}

	/**
	 * Creates an instance of {@link BinaryLogicalOperator} with {@link BinaryExpression::LOWER_THAN}
	 * value
	 * @return BinaryLogicalOperator
	 */
	static function lowerThan()
	{
		return self::create(self::LOWER_THAN);
	}

	/**
	 * Creates an instance of {@link BinaryLogicalOperator} with
	 * {@link BinaryExpression::LOWER_OR_EQUALS} value
	 * @return BinaryLogicalOperator
	 */
	static function lowerOrEquals()
	{
		return self::create(self::LOWER_OR_EQUALS);
	}

	/**
	 * Creates an instance of {@link BinaryLogicalOperator} with {@link BinaryExpression::LIKE}
	 * value
	 * @return BinaryLogicalOperator
	 */
	static function like()
	{
		return self::create(self::LIKE);
	}

	/**
	 * Creates an instance of {@link BinaryLogicalOperator} with {@link BinaryExpression::NOT_LIKE}
	 * value
	 * @return BinaryLogicalOperator
	 */
	static function notLike()
	{
		return self::create(self::NOT_LIKE);
	}

	/**
	 * Creates an instance of {@link BinaryLogicalOperator} with {@link BinaryExpression::ILIKE}
	 * value
	 * @return BinaryLogicalOperator
	 */
	static function ilike()
	{
		return self::create(self::ILIKE);
	}

	/**
	 * Creates an instance of {@link BinaryLogicalOperator} with {@link BinaryExpression::NOT_ILIKE}
	 * value
	 * @return BinaryLogicalOperator
	 */
	static function notIlike()
	{
		return self::create(self::NOT_ILIKE);
	}

	/**
	 * Creates an instance of {@link BinaryLogicalOperator} with {@link BinaryExpression::SIMILAR_TO}
	 * value
	 * @return BinaryLogicalOperator
	 */
	static function similarTo()
	{
		return self::create(self::SIMILAR_TO);
	}

	/**
	 * Creates an instance of {@link BinaryLogicalOperator} with {@link BinaryExpression::NOT_SIMILAR_TO}
	 * value
	 * @return BinaryLogicalOperator
	 */
	static function notSimilarTo()
	{
		return self::create(self::NOT_SIMILAR_TO);
	}

	/**
	 * Creates an instance of {@link BinaryLogicalOperator} with {@link BinaryExpression::ADD}
	 * value
	 * @return BinaryLogicalOperator
	 */
	static function add()
	{
		return self::create(self::ADD);
	}

	/**
	 * Creates an instance of {@link BinaryLogicalOperator} with {@link BinaryExpression::SUBTRACT}
	 * value
	 * @return BinaryLogicalOperator
	 */
	static function substract()
	{
		return self::create(self::SUBSTRACT);
	}

	/**
	 * Creates an instance of {@link BinaryLogicalOperator} with {@link BinaryExpression::MULTIPLY}
	 * value
	 * @return BinaryLogicalOperator
	 */
	static function multiply()
	{
		return self::create(self::MULTIPLY);
	}

	/**
	 * Creates an instance of {@link BinaryLogicalOperator} with {@link BinaryExpression::DIVIDE}
	 * value
	 * @return BinaryLogicalOperator
	 */
	static function divide()
	{
		return self::create(self::DIVIDE);
	}

}

?>