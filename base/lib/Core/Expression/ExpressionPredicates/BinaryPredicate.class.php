<?php
/* ***********************************************************************************************
 *
 * Phoebius Framework
 *
 * **********************************************************************************************
 *
 * Copyright notice
 *
 ************************************************************************************************/

/**
 * Represents a predicate used in {@link BinaryExpression}
 * @ingroup ExpressionPredicates
 */
final class BinaryPredicate extends Predicate
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
	 * Creates an instance of {@link BinaryPredicate}
	 * @param string $id one of the class constants
	 * @return BinaryPredicatePredicate
	 */
	static function create($id)
	{
		return new self($id);
	}

	/**
	 * Creates an instance of {@link BinaryPredicate} with {@link BinaryExpression::EQUALS} value
	 * @return BinaryPredicate
	 */
	static function equals()
	{
		return self::create(self::EQUALS);
	}

	/**
	 * Creates an instance of {@link BinaryPredicate} with {@link BinaryExpression::NOT_EQUALS}
	 * value
	 * @return BinaryPredicate
	 */
	static function notEquals()
	{
		return self::create(self::NOT_EQUALS);
	}

	/**
	 * Creates an instance of {@link BinaryPredicate} with {@link BinaryExpression::EXPRESSION_AND}
	 * value
	 * @return BinaryPredicate
	 */
	static function expAnd()
	{
		return self::create(self::EXPRESSION_AND);
	}

	/**
	 * Creates an instance of {@link BinaryPredicate} with {@link BinaryExpression::EXPRESSION_OR}
	 * value
	 * @return BinaryPredicate
	 */
	static function expOr()
	{
		return self::create(self::EXPRESSION_OR);
	}

	/**
	 * Creates an instance of {@link BinaryPredicate} with {@link BinaryExpression::GREATER_THAN}
	 * value
	 * @return BinaryPredicate
	 */
	static function greaterThan()
	{
		return self::create(self::GREATER_THAN);
	}

	/**
	 * Creates an instance of {@link BinaryPredicate} with
	 * {@link BinaryExpression::GREATER_THAN_OR_EQUALS} value
	 * @return BinaryPredicate
	 */
	static function greaterOrEquals()
	{
		return self::create(self::GREATER_OR_EQUALS);
	}

	/**
	 * Creates an instance of {@link BinaryPredicate} with {@link BinaryExpression::LOWER_THAN}
	 * value
	 * @return BinaryPredicate
	 */
	static function lowerThan()
	{
		return self::create(self::LOWER_THAN);
	}

	/**
	 * Creates an instance of {@link BinaryPredicate} with
	 * {@link BinaryExpression::LOWER_OR_EQUALS} value
	 * @return BinaryPredicate
	 */
	static function lowerOrEquals()
	{
		return self::create(self::LOWER_OR_EQUALS);
	}

	/**
	 * Creates an instance of {@link BinaryPredicate} with {@link BinaryExpression::LIKE}
	 * value
	 * @return BinaryPredicate
	 */
	static function like()
	{
		return self::create(self::LIKE);
	}

	/**
	 * Creates an instance of {@link BinaryPredicate} with {@link BinaryExpression::NOT_LIKE}
	 * value
	 * @return BinaryPredicate
	 */
	static function notLike()
	{
		return self::create(self::NOT_LIKE);
	}

	/**
	 * Creates an instance of {@link BinaryPredicate} with {@link BinaryExpression::ILIKE}
	 * value
	 * @return BinaryPredicate
	 */
	static function ilike()
	{
		return self::create(self::ILIKE);
	}

	/**
	 * Creates an instance of {@link BinaryPredicate} with {@link BinaryExpression::NOT_ILIKE}
	 * value
	 * @return BinaryPredicate
	 */
	static function notIlike()
	{
		return self::create(self::NOT_ILIKE);
	}

	/**
	 * Creates an instance of {@link BinaryPredicate} with {@link BinaryExpression::SIMILAR_TO}
	 * value
	 * @return BinaryPredicate
	 */
	static function similarTo()
	{
		return self::create(self::SIMILAR_TO);
	}

	/**
	 * Creates an instance of {@link BinaryPredicate} with {@link BinaryExpression::NOT_SIMILAR_TO}
	 * value
	 * @return BinaryPredicate
	 */
	static function notSimilarTo()
	{
		return self::create(self::NOT_SIMILAR_TO);
	}

	/**
	 * Creates an instance of {@link BinaryPredicate} with {@link BinaryExpression::ADD}
	 * value
	 * @return BinaryPredicate
	 */
	static function add()
	{
		return self::create(self::ADD);
	}

	/**
	 * Creates an instance of {@link BinaryPredicate} with {@link BinaryExpression::SUBTRACT}
	 * value
	 * @return BinaryPredicate
	 */
	static function substract()
	{
		return self::create(self::SUBSTRACT);
	}

	/**
	 * Creates an instance of {@link BinaryPredicate} with {@link BinaryExpression::MULTIPLY}
	 * value
	 * @return BinaryPredicate
	 */
	static function multiply()
	{
		return self::create(self::MULTIPLY);
	}

	/**
	 * Creates an instance of {@link BinaryPredicate} with {@link BinaryExpression::DIVIDE}
	 * value
	 * @return BinaryPredicate
	 */
	static function divide()
	{
		return self::create(self::DIVIDE);
	}

}

?>