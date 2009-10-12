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
	static function eq($subject, $value)
	{
		return new BinaryExpression($subject, BinaryPredicate::equals(), $value);
	}

	/**
	 * Creates an instance of {@link BinaryExpression} with strict equality
	 * @return BinaryExpression
	 */
	static function neq($subject, $value)
	{
		return new BinaryExpression($subject, BinaryPredicate::notEquals(), $value);
	}

	/**
	 * Creates an instance of {@link BinaryExpression} with `greater than` predicate
	 * @return BinaryExpression
	 */
	static function gt($subject, $value)
	{
		return new BinaryExpression($subject, BinaryPredicate::greaterThan(), $value);
	}

	/**
	 * Creates an instance of {@link BinaryExpression} with `greater than or equals` predicate
	 * @return BinaryExpression
	 */
	static function gtEq($subject, $value)
	{
		return new BinaryExpression($subject, BinaryPredicate::greaterOrEquals(), $value);
	}

	/**
	 * Creates an instance of {@link BinaryExpression} with `lower than` predicate
	 * @return BinaryExpression
	 */
	static function lt($subject, $value)
	{
		return new BinaryExpression($subject, BinaryPredicate::lowerThan(), $value);
	}

	/**
	 * Creates an instance of {@link BinaryExpression} with `lower than or equals` predicate
	 * @return BinaryExpression
	 */
	static function ltEq($subject, $value)
	{
		return new BinaryExpression($subject, BinaryPredicate::lowerOrEquals(), $value);
	}

	/**
	 * Creates an instnace of {@link BinaryExpression} with `like` predicate
	 * @return BinaryExpression
	 */
	static function like($subject, $value)
	{
		return new BinaryExpression($subject, BinaryPredicate::like(), $value);
	}

	/**
	 * Creates an instnace of {@link BinaryExpression} with `like` predicate
	 * @return BinaryExpression
	 */
	static function notLike($subject, $value)
	{
		return new BinaryExpression($subject, BinaryPredicate::notIlike(), $value);
	}

	/**
	 * Creates an instnace of {@link BinaryExpression} with case-insensitive `like` predicate
	 * @return BinaryExpression
	 */
	static function ilike($subject, $value)
	{
		return new BinaryExpression($subject, BinaryPredicate::ilike(), $value);
	}

	/**
	 * Creates an instnace of {@link BinaryExpression} with case-insensitive `not like` predicate
	 * @return BinaryExpression
	 */
	static function notIlike($subject, $value)
	{
		return new BinaryExpression($subject, BinaryPredicate::notIlike(), $value);
	}

	/**
	 * Creates an instnace of {@link BinaryExpression} with `similar to` predicate
	 * @return BinaryExpression
	 */
	static function similar($subject, $value)
	{
		return new BinaryExpression($subject, BinaryPredicate::similarTo(), $value);
	}

	/**
	 * Creates an instnace of {@link BinaryExpression} with inverted `similar to` predicate
	 * @return BinaryExpression
	 */
	static function notSimilar($subject, $value)
	{
		return new BinaryExpression($subject, BinaryPredicate::notSimilarTo(), $value);
	}

	/**
	 * Creates an instance of {@link BinaryExpression} with "plus" operator
	 * @return BinaryExpression
	 */
	static function add($subject, $value)
	{
		return new BinaryExpression($subject, BinaryPredicate::add(), $value);
	}

	/**
	 * Creates an instance of {@link BinaryExpression} with "minus" operator
	 * @return BinaryExpression
	 */
	static function sub($subject, $value)
	{
		return new BinaryExpression($subject, BinaryPredicate::substract(), $value);
	}

	/**
	 * Creates an instance of {@link BinaryExpression} with "multiply" operator
	 * @return BinaryExpression
	 */
	static function mul($subject, $value)
	{
		return new BinaryExpression($subject, BinaryPredicate::multiply(), $value);
	}

	/**
	 * Creates an instance of {@link BinaryExpression} with "division" operator
	 * @return BinaryExpression
	 */
	static function div($subject, $value)
	{
		return new BinaryExpression($subject, BinaryPredicate::divide(), $value);
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
	static function between($subject, $from, $to)
	{
		return new BetweenRangeExpression($subject, $from, $to);
	}

	/**
	 * Helper method to check whether the field value is in the specified set of values. Strict
	 * equality is used in comparison
	 * @return IDalExpression
	 */
	static function in($subject, $set)
	{
		return new InSetExpression($subject, $set, InSetPredicate::in());
	}

	/**
	 * Helper method to check whether the field value is in the specified set of values. Strict
	 * equality is used in comparison
	 * @return IDalExpression
	 */
	static function notIn($subject, $set)
	{
		return new InSetExpression($subject, $set, InSetPredicate::notIn());
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

	/**
	 * Creates a block of {@link IExpression} arguments joined with `OR` predicate and
	 * wrapped by {@link ExpressionChain}
	 * @param IExpression ...
	 * @return ExpressionChain
	 */
	static function joinByOr()
	{
		$args = func_get_args();
		$chain = self::orChain();
		foreach ($args as $arg) {
			$chain->add($arg);
		}

		return $chain;
	}

	/**
	 * Creates a block of {@link IExpression} arguments joined with `AND` predicate and
	 * wrapped by {@link ExpressionChain}
	 * @param IExpression ...
	 * @return ExpressionChain
	 */
	static function joinByAnd()
	{
		$args = func_get_args();
		$chain = self::andChain();
		foreach ($args as $arg) {
			$chain->add($arg);
		}
		return $chain;
	}

	/**
	 * Creates an instance of {@link ExpressionChain} with `OR` predicate
	 * @return ExpressionChain
	 */
	static function orChain()
	{
		return new ExpressionChain(ExpressionChainPredicate::conditionOr());
	}

	/**
	 * Creates an instance of {@link ExpressionChain} with `AND` predicate
	 * @return ExpressionChain
	 */
	static function andChain()
	{
		return new ExpressionChain(ExpressionChainPredicate::conditionAnd());
	}
}

?>