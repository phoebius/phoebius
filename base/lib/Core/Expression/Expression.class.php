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
 * Helper class to access expression classes
 * @ingroup BaseExpression
 */
final class Expression extends StaticClass
{
	/**
	 * Creates an instance of {@link BinaryExpression} with strict equality
	 * @return BinaryExpression
	 */
	static function eq($value)
	{
		return new BinaryExpression($value, BinaryPredicate::equals());
	}

	/**
	 * Creates an instance of {@link BinaryExpression} with strict equality between field and
	 * an identifier
	 * @return BinaryExpression
	 */
	static function eqId(IIdentifiable $object)
	{
		return self::eq($object->getId());
	}

	/**
	 * Creates an instance of {@link BinaryExpression} with inverted equality
	 * @return BinaryExpression
	 */
	static function notEq(IIdentifiable $value)
	{
		return new BinaryExpression($value, BinaryPredicate::notEquals());
	}

	/**
	 * Creates an instance of {@link BinaryExpression} with `greater than` predicate
	 * @return BinaryExpression
	 */
	static function gt($value)
	{
		return new BinaryExpression($value, BinaryPredicate::greaterThan());
	}

	/**
	 * Creates an instance of {@link BinaryExpression} with `greater than or equals` predicate
	 * @return BinaryExpression
	 */
	static function gtEq($value)
	{
		return new BinaryExpression($value, BinaryPredicate::greaterOrEquals());
	}

	/**
	 * Creates an instance of {@link BinaryExpression} with `lower than` predicate
	 * @return BinaryExpression
	 */
	static function lt($value)
	{
		return new BinaryExpression($value, BinaryPredicate::lowerThan());
	}

	/**
	 * Creates an instance of {@link BinaryExpression} with `lower than or equals` predicate
	 * @return BinaryExpression
	 */
	static function ltEq($value)
	{
		return new BinaryExpression($value, BinaryPredicate::lowerOrEquals());
	}

	/**
	 * Creates an instnace of {@link BinaryExpression} with `like` predicate
	 * @return BinaryExpression
	 */
	static function like($value)
	{
		return new BinaryExpression($value, BinaryPredicate::like());
	}

	/**
	 * Creates an instnace of {@link BinaryExpression} with `like` predicate
	 * @return BinaryExpression
	 */
	static function notLike($value)
	{
		return new BinaryExpression($value, BinaryPredicate::notIlike());
	}

	/**
	 * Creates an instnace of {@link BinaryExpression} with case-insensitive `like` predicate
	 * @return BinaryExpression
	 */
	static function ilike($value)
	{
		return new BinaryExpression($value, BinaryPredicate::ilike());
	}

	/**
	 * Creates an instnace of {@link BinaryExpression} with case-insensitive `not like` predicate
	 * @return BinaryExpression
	 */
	static function notIlike($value)
	{
		return new BinaryExpression($value, BinaryPredicate::notIlike());
	}

	/**
	 * Creates an instnace of {@link BinaryExpression} with `similar to` predicate
	 * @return BinaryExpression
	 */
	static function similar($value)
	{
		return new BinaryExpression($value, BinaryPredicate::similarTo());
	}

	/**
	 * Creates an instnace of {@link BinaryExpression} with inverted `similar to` predicate
	 * @return BinaryExpression
	 */
	static function notSimilar($value)
	{
		return new BinaryExpression($value, BinaryPredicate::notSimilarTo());
	}

	/**
	 * Creates an instance of {@link BinaryExpression} with "plus" operator
	 * @return BinaryExpression
	 */
	static function add($value)
	{
		return new BinaryExpression($value, BinaryPredicate::add());
	}

	/**
	 * Creates an instance of {@link BinaryExpression} with "minus" operator
	 * @return BinaryExpression
	 */
	static function sub($value)
	{
		return new BinaryExpression($value, BinaryPredicate::substract());
	}

	/**
	 * Creates an instance of {@link BinaryExpression} with "multiply" operator
	 * @return BinaryExpression
	 */
	static function mul($value)
	{
		return new BinaryExpression($value, BinaryPredicate::multiply());
	}

	/**
	 * Creates an instance of {@link BinaryExpression} with "division" operator
	 * @return BinaryExpression
	 */
	static function div($value)
	{
		return new BinaryExpression($value, BinaryPredicate::divide());
	}

	/**
	 * Creates an instance of `not null` unary postfix expression
	 * @return UnaryPostfixExpression
	 */
	static function notNull($subject)
	{
		return new UnaryPostfixExpression($subject, UnaryPostfixPredicate::isNotNull());
	}

	/**
	 * Creates an instance of `is null` unary postfix expression
	 * @return UnaryPostfixExpression
	 */
	static function isNull($subject)
	{
		return new UnaryPostfixExpression($subject, UnaryPostfixPredicate::isNull());
	}

	/**
	 * Creates an instance of `is true` unary postfix expression
	 * @return UnaryPostfixExpression
	 */
	static function isTrue($subject)
	{
		return new UnaryPostfixExpression($subject, UnaryPostfixPredicate::isTrue());
	}

	/**
	 * Creates an instance of `is false` unary postfix expression
	 * @return UnaryPostfixExpression
	 */
	static function isFalse($subject)
	{
		return new UnaryPostfixExpression($subject, UnaryPostfixPredicate::isFalse());
	}

	/**
	 * Creates an instance of {@link BetweenRangeExpression}
	 * @return BetweenRangeExpression
	 */
	static function between($from, $to)
	{
		return new BetweenRangeExpression($from, $to);
	}

	/**
	 * Helper method to check whether the field value is in the specified set of values. Strict
	 * equality is used in comparison
	 * @return IDalExpression
	 */
	static function in($set)
	{
		return new InSetExpression($set, InSetPredicate::in());
	}

	/**
	 * Helper method to check whether the field value is in the specified set of values. Strict
	 * equality is used in comparison
	 * @return IDalExpression
	 */
	static function notIn($set)
	{
		return new InSetExpression($set, InSetPredicate::notIn());
	}

	/**
	 * Creates an instance of {@link PrefixUnaryExpression} with prefixed "NOT" predicate to
	 * invert the value
	 * @return PrefixUnaryExpression
	 */
	static function not($subject)
	{
		return new PrefixUnaryExpression(PrefixUnaryPredicate::not(), $subject);
	}

	/**
	 * Creates an instance of {@link PrefixUnaryExpression} with prefixed "MINUS" predicate to
	 * treat the field value as negative
	 * @return PrefixUnaryExpression
	 */
	static function negative($subject)
	{
		return new PrefixUnaryExpression(PrefixUnaryPredicate::minus(), $subject);
	}
}

?>