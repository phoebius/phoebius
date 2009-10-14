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
		return new BinaryExpression($subject, BinaryLogicalOperator::equals(), $value);
	}

	/**
	 * Creates an instance of {@link BinaryExpression} with strict equality
	 * @return BinaryExpression
	 */
	static function neq($subject, $value)
	{
		return new BinaryExpression($subject, BinaryLogicalOperator::notEquals(), $value);
	}

	/**
	 * Creates an instance of {@link BinaryExpression} with `greater than` logical operator
	 * @return BinaryExpression
	 */
	static function gt($subject, $value)
	{
		return new BinaryExpression($subject, BinaryLogicalOperator::greaterThan(), $value);
	}

	/**
	 * Creates an instance of {@link BinaryExpression} with `greater than or equals` logical operator
	 * @return BinaryExpression
	 */
	static function gtEq($subject, $value)
	{
		return new BinaryExpression($subject, BinaryLogicalOperator::greaterOrEquals(), $value);
	}

	/**
	 * Creates an instance of {@link BinaryExpression} with `lower than` logical operator
	 * @return BinaryExpression
	 */
	static function lt($subject, $value)
	{
		return new BinaryExpression($subject, BinaryLogicalOperator::lowerThan(), $value);
	}

	/**
	 * Creates an instance of {@link BinaryExpression} with `lower than or equals` logical operator
	 * @return BinaryExpression
	 */
	static function ltEq($subject, $value)
	{
		return new BinaryExpression($subject, BinaryLogicalOperator::lowerOrEquals(), $value);
	}

	/**
	 * Creates an instnace of {@link BinaryExpression} with `like` logical operator
	 * @return BinaryExpression
	 */
	static function like($subject, $value)
	{
		return new BinaryExpression($subject, BinaryLogicalOperator::like(), $value);
	}

	/**
	 * Creates an instnace of {@link BinaryExpression} with `like` logical operator
	 * @return BinaryExpression
	 */
	static function notLike($subject, $value)
	{
		return new BinaryExpression($subject, BinaryLogicalOperator::notIlike(), $value);
	}

	/**
	 * Creates an instnace of {@link BinaryExpression} with case-insensitive `like` logical operator
	 * @return BinaryExpression
	 */
	static function ilike($subject, $value)
	{
		return new BinaryExpression($subject, BinaryLogicalOperator::ilike(), $value);
	}

	/**
	 * Creates an instnace of {@link BinaryExpression} with case-insensitive `not like` logical operator
	 * @return BinaryExpression
	 */
	static function notIlike($subject, $value)
	{
		return new BinaryExpression($subject, BinaryLogicalOperator::notIlike(), $value);
	}

	/**
	 * Creates an instnace of {@link BinaryExpression} with `similar to` logical operator
	 * @return BinaryExpression
	 */
	static function similar($subject, $value)
	{
		return new BinaryExpression($subject, BinaryLogicalOperator::similarTo(), $value);
	}

	/**
	 * Creates an instnace of {@link BinaryExpression} with inverted `similar to` logical operator
	 * @return BinaryExpression
	 */
	static function notSimilar($subject, $value)
	{
		return new BinaryExpression($subject, BinaryLogicalOperator::notSimilarTo(), $value);
	}

	/**
	 * Creates an instance of {@link BinaryExpression} with "plus" operator
	 * @return BinaryExpression
	 */
	static function add($subject, $value)
	{
		return new BinaryExpression($subject, BinaryLogicalOperator::add(), $value);
	}

	/**
	 * Creates an instance of {@link BinaryExpression} with "minus" operator
	 * @return BinaryExpression
	 */
	static function sub($subject, $value)
	{
		return new BinaryExpression($subject, BinaryLogicalOperator::substract(), $value);
	}

	/**
	 * Creates an instance of {@link BinaryExpression} with "multiply" operator
	 * @return BinaryExpression
	 */
	static function mul($subject, $value)
	{
		return new BinaryExpression($subject, BinaryLogicalOperator::multiply(), $value);
	}

	/**
	 * Creates an instance of {@link BinaryExpression} with "division" operator
	 * @return BinaryExpression
	 */
	static function div($subject, $value)
	{
		return new BinaryExpression($subject, BinaryLogicalOperator::divide(), $value);
	}

	/**
	 * Creates an instance of `not null` unary postfix expression
	 * @return UnaryPostfixExpression
	 */
	static function notNull($subject)
	{
		return new UnaryPostfixExpression($subject, UnaryPostfixLogicalOperator::isNotNull());
	}

	/**
	 * Creates an instance of `is null` unary postfix expression
	 * @return UnaryPostfixExpression
	 */
	static function isNull($subject)
	{
		return new UnaryPostfixExpression($subject, UnaryPostfixLogicalOperator::isNull());
	}

	/**
	 * Creates an instance of `is true` unary postfix expression
	 * @return UnaryPostfixExpression
	 */
	static function isTrue($subject)
	{
		return new UnaryPostfixExpression($subject, UnaryPostfixLogicalOperator::isTrue());
	}

	/**
	 * Creates an instance of `is false` unary postfix expression
	 * @return UnaryPostfixExpression
	 */
	static function isFalse($subject)
	{
		return new UnaryPostfixExpression($subject, UnaryPostfixLogicalOperator::isFalse());
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
		return new InSetExpression($subject, $set, InSetLogicalOperator::in());
	}

	/**
	 * Helper method to check whether the field value is in the specified set of values. Strict
	 * equality is used in comparison
	 * @return IDalExpression
	 */
	static function notIn($subject, $set)
	{
		return new InSetExpression($subject, $set, InSetLogicalOperator::notIn());
	}

	/**
	 * Creates an instance of {@link PrefixUnaryExpression} with prefixed "NOT" logical operator to
	 * invert the value
	 * @return PrefixUnaryExpression
	 */
	static function not($subject)
	{
		return new PrefixUnaryExpression(PrefixUnaryLogicalOperator::not(), $subject);
	}

	/**
	 * Creates an instance of {@link PrefixUnaryExpression} with prefixed "MINUS" logical operator to
	 * treat the field value as negative
	 * @return PrefixUnaryExpression
	 */
	static function negative($subject)
	{
		return new PrefixUnaryExpression(PrefixUnaryLogicalOperator::minus(), $subject);
	}

	/**
	 * Creates a block of {@link IExpression} arguments joined with `OR` logical operator and
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
	 * Creates a block of {@link IExpression} arguments joined with `AND` logical operator and
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
	 * Creates an instance of {@link ExpressionChain} with `OR` logical operator
	 * @return ExpressionChain
	 */
	static function orChain()
	{
		return new ExpressionChain(ExpressionChainLogicalOperator::conditionOr());
	}

	/**
	 * Creates an instance of {@link ExpressionChain} with `AND` logical operator
	 * @return ExpressionChain
	 */
	static function andChain()
	{
		return new ExpressionChain(ExpressionChainLogicalOperator::conditionAnd());
	}
}

?>