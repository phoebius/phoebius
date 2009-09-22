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
 * @ingroup Condition
 */
final class Expression extends StaticClass
{
	/**
	 * Creates an instance of {@link BinaryExpression} with `and` predicate
	 * @param SqlColumn $field
	 * @param ISqlValueExpression $value value to be compared. In most cases, {@link SqlValue} is
	 * 	needed here, but expressions are allowed to (e.g., {@link SelectQuery})
	 * @return BinaryExpression
	 */
	static function expAnd(SqlColumn $field, ISqlValueExpression $value)
	{
		return new BinaryExpression($field, $value, BinaryPredicate::expAnd());
	}

	/**
	 * Creates an instance of {@link BinaryExpression} with `Or` predicate
	 * @param SqlColumn $field
	 * @param ISqlValueExpression $value value to be compared. In most cases, {@link SqlValue} is
	 * 	needed here, but expressions are allowed to (e.g., {@link SelectQuery})
	 * @return BinaryExpression
	 */
	static function expOr(SqlColumn $field, ISqlValueExpression $value)
	{
		return new BinaryExpression($field, $value, BinaryPredicate::expOr());
	}

	/**
	 * Creates an instance of {@link BinaryExpression} with strict equality
	 * @param SqlColumn $field
	 * @param ISqlValueExpression $value value to be compared. In most cases, {@link SqlValue} is
	 * 	needed here, but expressions are allowed to (e.g., {@link SelectQuery})
	 * @return BinaryExpression
	 */
	static function eq(SqlColumn $field, ISqlValueExpression $value)
	{
		return new BinaryExpression($field, $value, BinaryPredicate::equals());
	}

	/**
	 * Creates an instance of {@link BinaryExpression} with strict equality between field and
	 * an identifier
	 * @return BinaryExpression
	 */
	static function eqId(SqlColumn $field, IIdentifiable $object)
	{
		return self::eq($field, $object->getId());
	}

	/**
	 * Creates an instance of {@link BinaryExpression} with inverted equality
	 * @return BinaryExpression
	 */
	static function notEq(SqlColumn $field, IIdentifiable $value)
	{
		return new BinaryExpression($field, $value, BinaryPredicate::notEquals());
	}

	/**
	 * Creates an instance of {@link BinaryExpression} with `greater than` predicate
	 * @param SqlColumn $field
	 * @param ISqlValueExpression $value value to be compared. In most cases, {@link SqlValue} is
	 * 	needed here, but expressions are allowed to (e.g., {@link SelectQuery})
	 * @return BinaryExpression
	 */
	static function gt(SqlColumn $field, ISqlValueExpression $value)
	{
		return new BinaryExpression($field, $value, BinaryPredicate::greaterThan());
	}

	/**
	 * Creates an instance of {@link BinaryExpression} with `greater than or equals` predicate
	 * @param SqlColumn $field
	 * @param ISqlValueExpression $value value to be compared. In most cases, {@link SqlValue} is
	 * 	needed here, but expressions are allowed to (e.g., {@link SelectQuery})
	 * @return BinaryExpression
	 */
	static function gtEq(SqlColumn $field, ISqlValueExpression $value)
	{
		return new BinaryExpression($field, $value, BinaryPredicate::greaterOrEquals());
	}

	/**
	 * Creates an instance of {@link BinaryExpression} with `lower than` predicate
	 * @param SqlColumn $field
	 * @param ISqlValueExpression $value value to be compared. In most cases, {@link SqlValue} is
	 * 	needed here, but expressions are allowed to (e.g., {@link SelectQuery})
	 * @return BinaryExpression
	 */
	static function lt(SqlColumn $field, ISqlValueExpression $value)
	{
		return new BinaryExpression($field, $value, BinaryPredicate::lowerThan());
	}

	/**
	 * Creates an instance of {@link BinaryExpression} with `lower than or equals` predicate
	 * @param SqlColumn $field
	 * @param ISqlValueExpression $value value to be compared. In most cases, {@link SqlValue} is
	 * 	needed here, but expressions are allowed to (e.g., {@link SelectQuery})
	 * @return BinaryExpression
	 */
	static function ltEq(SqlColumn $field, ISqlValueExpression $value)
	{
		return new BinaryExpression($field, $value, BinaryPredicate::lowerOrEquals());
	}

	/**
	 * Creates an instnace of {@link BinaryExpression} with `like` predicate
	 * @param SqlColumn $field
	 * @param ISqlValueExpression $value value to be compared. In most cases, {@link SqlValue} is
	 * 	needed here, but expressions are allowed to (e.g., {@link SelectQuery})
	 * @return BinaryExpression
	 */
	static function like(SqlColumn $field, ISqlValueExpression $value)
	{
		return new BinaryExpression($field, $value, BinaryPredicate::like());
	}

	/**
	 * Creates an instnace of {@link BinaryExpression} with `like` predicate
	 * @param SqlColumn $field
	 * @param ISqlValueExpression $value value to be compared. In most cases, {@link SqlValue} is
	 * 	needed here, but expressions are allowed to (e.g., {@link SelectQuery})
	 * @return BinaryExpression
	 */
	static function notLike(SqlColumn $field, ISqlValueExpression $value)
	{
		return new BinaryExpression($field, $value, BinaryPredicate::notIlike());
	}

	/**
	 * Creates an instnace of {@link BinaryExpression} with case-insensitive `like` predicate
	 * @param SqlColumn $field
	 * @param ISqlValueExpression $value value to be compared. In most cases, {@link SqlValue} is
	 * 	needed here, but expressions are allowed to (e.g., {@link SelectQuery})
	 * @return BinaryExpression
	 */
	static function ilike(SqlColumn $field, ISqlValueExpression $value)
	{
		return new BinaryExpression($field, $value, BinaryPredicate::ilike());
	}

	/**
	 * Creates an instnace of {@link BinaryExpression} with case-insensitive `not like` predicate
	 * @param SqlColumn $field
	 * @param ISqlValueExpression $value value to be compared. In most cases, {@link SqlValue} is
	 * 	needed here, but expressions are allowed to (e.g., {@link SelectQuery})
	 * @return BinaryExpression
	 */
	static function notIlike(SqlColumn $field, ISqlValueExpression $value)
	{
		return new BinaryExpression($field, $value, BinaryPredicate::notIlike());
	}

	/**
	 * Creates an instnace of {@link BinaryExpression} with `similar to` predicate
	 * @param SqlColumn $field
	 * @param ISqlValueExpression $value value to be compared. In most cases, {@link SqlValue} is
	 * 	needed here, but expressions are allowed to (e.g., {@link SelectQuery})
	 * @return BinaryExpression
	 */
	static function similar(SqlColumn $field, ISqlValueExpression $value)
	{
		return new BinaryExpression($field, $value, BinaryPredicate::similarTo());
	}

	/**
	 * Creates an instnace of {@link BinaryExpression} with inverted `similar to` predicate
	 * @param SqlColumn $field
	 * @param ISqlValueExpression $value value to be compared. In most cases, {@link SqlValue} is
	 * 	needed here, but expressions are allowed to (e.g., {@link SelectQuery})
	 * @return BinaryExpression
	 */
	static function notSimilar(SqlColumn $field, ISqlValueExpression $value)
	{
		return new BinaryExpression($field, $value, BinaryPredicate::notSimilarTo());
	}

	/**
	 * Creates an instance of {@link BinaryExpression} with "plus" operator
	 * @param SqlColumn $field
	 * @param ISqlValueExpression $value value to be compared. In most cases, {@link SqlValue} is
	 * 	needed here, but expressions are allowed to (e.g., {@link SelectQuery})
	 * @return BinaryExpression
	 */
	static function add(SqlColumn $field, ISqlValueExpression $value)
	{
		return new BinaryExpression($field, $value, BinaryPredicate::add());
	}

	/**
	 * Creates an instance of {@link BinaryExpression} with "minus" operator
	 * @param SqlColumn $field
	 * @param ISqlValueExpression $value value to be compared. In most cases, {@link SqlValue} is
	 * 	needed here, but expressions are allowed to (e.g., {@link SelectQuery})
	 * @return BinaryExpression
	 */
	static function sub(SqlColumn $field, ISqlValueExpression $value)
	{
		return new BinaryExpression($field, $value, BinaryPredicate::substract());
	}

	/**
	 * Creates an instance of {@link BinaryExpression} with "multiply" operator
	 * @param SqlColumn $field
	 * @param ISqlValueExpression $value value to be compared. In most cases, {@link SqlValue} is
	 * 	needed here, but expressions are allowed to (e.g., {@link SelectQuery})
	 * @return BinaryExpression
	 */
	static function mul(SqlColumn $field, ISqlValueExpression $value)
	{
		return new BinaryExpression($field, $value, BinaryPredicate::multiply());
	}

	/**
	 * Creates an instance of {@link BinaryExpression} with "division" operator
	 * @param SqlColumn $field
	 * @param ISqlValueExpression $value value to be compared. In most cases, {@link SqlValue} is
	 * 	needed here, but expressions are allowed to (e.g., {@link SelectQuery})
	 * @return BinaryExpression
	 */
	static function div(SqlColumn $field, ISqlValueExpression $value)
	{
		return new BinaryExpression($field, $value, BinaryPredicate::divide());
	}

	/**
	 * Creates an instance of `not null` unary postfix expression
	 * @param ISqlValueExpression $subject probably, {@link SqlColumn}, but can be either
	 * 	{@link SelectQuery} or any other sql expression
	 * @return UnaryPostfixExpression
	 */
	static function notNull(ISqlValueExpression $subject)
	{
		return new UnaryPostfixExpression($subject, UnaryPostfixPredicate::isNotNull());
	}

	/**
	 * Creates an instance of `is null` unary postfix expression
	 * @param ISqlValueExpression $subject probably, {@link SqlColumn}, but can be either
	 * 	{@link SelectQuery} or any other sql expression
	 * @return UnaryPostfixExpression
	 */
	static function isNull(ISqlValueExpression $subject)
	{
		return new UnaryPostfixExpression($subject, UnaryPostfixPredicate::isNull());
	}

	/**
	 * Creates an instance of `is true` unary postfix expression
	 * @param ISqlValueExpression $subject probably, {@link SqlColumn}, but can be either
	 * 	{@link SelectQuery} or any other sql expression
	 * @return UnaryPostfixExpression
	 */
	static function isTrue(ISqlValueExpression $subject)
	{
		return new UnaryPostfixExpression($subject, UnaryPostfixPredicate::isTrue());
	}

	/**
	 * Creates an instance of `is false` unary postfix expression
	 * @param ISqlValueExpression $subject probably, {@link SqlColumn}, but can be either
	 * 	{@link SelectQuery} or any other sql expression
	 * @return UnaryPostfixExpression
	 */
	static function isFalse(ISqlValueExpression $subject)
	{
		return new UnaryPostfixExpression($subject, UnaryPostfixPredicate::isFalse());
	}

	/**
	 * Creates an instance of {@link BetweenRangeExpression}
	 * @param SqlColumn $field
	 * @param ISqlValueExpression $from starting value in range. In most cases, {@link SqlValue} is
	 * 	needed here, but expressions are allowed to (e.g., {@link SelectQuery})
	 * @param ISqlValueExpression $to ending value in range. In most cases, {@link SqlValue} is
	 * 	needed here, but expressions are allowed to (e.g., {@link SelectQuery})
	 * @return BetweenRangeExpression
	 */
	static function between(SqlColumn $field, ISqlValueExpression $from, ISqlValueExpression $to)
	{
		return new BetweenRangeExpression($field, $from, $to);
	}

	/**
	 * Helper method to check whether the field value is in the specified set of values. Strict
	 * equality is used in comparison
	 * @param SqlColumn $field
	 * @param ISqlValueExpression $set representing a set of values, In most cases,
	 * 	{@link SqlValueList} is needed here, but can also be used other Sql-compatible
	 * 	expressions, like {@link SelectQuery}
	 * @return ISqlLogicalExpression
	 */
	static function in(SqlColumn $field, ISqlValueExpression $set)
	{
		return new InSetExpression($field, $set, InSetPredicate::in());
	}

	/**
	 * Helper method to check whether the field value is in the specified set of values. Strict
	 * equality is used in comparison
	 * @param SqlColumn $field
	 * @param ISqlValueExpression $set representing a set of values, In most cases,
	 * 	{@link SqlValueList} is needed here, but can also be used other Sql-compatible
	 * 	expressions, like {@link SelectQuery}
	 * @return ISqlLogicalExpression
	 */
	static function notIn(SqlColumn $field, ISqlValueExpression $set)
	{
		return new InSetExpression($field, $set, InSetPredicate::notIn());
	}

	/**
	 * Creates a block of {@link ISqlLogicalExpression} arguments joined with `OR` predicate and
	 * wrapped by {@link ExpressionChain}
	 * @param ISqlLogicalExpression ...
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
	 * Creates a block of {@link ISqlLogicalExpression} arguments joined with `AND` predicate and
	 * wrapped by {@link ExpressionChain}
	 * @param ISqlLogicalExpression ...
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

	/**
	 * Creates an instance of {@link PrefixUnaryExpression} with prefixed "NOT" predicate to
	 * invert the value
	 * @param ISqlValueExpression $subject probably, {@link SqlColumn}, but can be either
	 * 	{@link SelectQuery} or any other sql expression
	 * @return PrefixUnaryExpression
	 */
	static function not(ISqlValueExpression $subject)
	{
		return new PrefixUnaryExpression(PrefixUnaryPredicate::not(), $subject);
	}

	/**
	 * Creates an instance of {@link PrefixUnaryExpression} with prefixed "MINUS" predicate to
	 * treat the field value as negative
	 * @param ISqlValueExpression $subject probably, {@link SqlColumn}, but can be either
	 * 	{@link SelectQuery} or any other sql expression
	 * @return PrefixUnaryExpression
	 */
	static function negative(ISqlValueExpression $subject)
	{
		return new PrefixUnaryExpression(PrefixUnaryPredicate::minus(), $subject);
	}
}

?>