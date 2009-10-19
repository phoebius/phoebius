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
 * Helper class to access expression classes
 * @ingroup Dal_DB_Expression
 */
final class DalExpression extends StaticClass
{
	/**
	 * Creates an instance of {@link BinaryDalExpression} with `and` logical operator
	 * @param SqlColumn $field
	 * @param ISqlValueExpression $value value to be compared. In most cases, {@link SqlValue} is
	 * 	needed here, but expressions are allowed to (e.g., {@link SelectQuery})
	 * @return BinaryDalExpression
	 */
	static function expAnd(SqlColumn $field, ISqlValueExpression $value)
	{
		return new BinaryDalExpression(new BinaryExpression($field, BinaryLogicalOperator::expAnd(), $value));
	}

	/**
	 * Creates an instance of {@link BinaryDalExpression} with `Or` logical operator
	 * @param SqlColumn $field
	 * @param ISqlValueExpression $value value to be compared. In most cases, {@link SqlValue} is
	 * 	needed here, but expressions are allowed to (e.g., {@link SelectQuery})
	 * @return BinaryDalExpression
	 */
	static function expOr(SqlColumn $field, ISqlValueExpression $value)
	{
		return new BinaryDalExpression(new BinaryExpression($field, BinaryLogicalOperator::expOr(), $value));
	}

	/**
	 * Creates an instance of {@link BinaryDalExpression} with strict equality
	 * @param SqlColumn $field
	 * @param ISqlValueExpression $value value to be compared. In most cases, {@link SqlValue} is
	 * 	needed here, but expressions are allowed to (e.g., {@link SelectQuery})
	 * @return BinaryDalExpression
	 */
	static function eq(SqlColumn $field, ISqlValueExpression $value)
	{
		return new BinaryDalExpression(new BinaryExpression($field, BinaryLogicalOperator::equals(), $value));
	}

	/**
	 * Creates an instance of {@link BinaryDalExpression} with strict equality between field and
	 * an identifier
	 * @return BinaryDalExpression
	 */
	static function eqId(SqlColumn $field, IIdentifiable $object)
	{
		return self::eq($field, $object->getId());
	}

	/**
	 * Creates an instance of {@link BinaryDalExpression} with inverted equality
	 * @return BinaryDalExpression
	 */
	static function notEq(SqlColumn $field, IIdentifiable $value)
	{
		return new BinaryDalExpression(new BinaryExpression($field, BinaryLogicalOperator::notEquals(), $value));
	}

	/**
	 * Creates an instance of {@link BinaryDalExpression} with `greater than` logical operator
	 * @param SqlColumn $field
	 * @param ISqlValueExpression $value value to be compared. In most cases, {@link SqlValue} is
	 * 	needed here, but expressions are allowed to (e.g., {@link SelectQuery})
	 * @return BinaryDalExpression
	 */
	static function gt(SqlColumn $field, ISqlValueExpression $value)
	{
		return new BinaryDalExpression(new BinaryExpression($field, BinaryLogicalOperator::greaterThan(), $value));
	}

	/**
	 * Creates an instance of {@link BinaryDalExpression} with `greater than or equals` logical operator
	 * @param SqlColumn $field
	 * @param ISqlValueExpression $value value to be compared. In most cases, {@link SqlValue} is
	 * 	needed here, but expressions are allowed to (e.g., {@link SelectQuery})
	 * @return BinaryDalExpression
	 */
	static function gtEq(SqlColumn $field, ISqlValueExpression $value)
	{
		return new BinaryDalExpression(new BinaryExpression($field, BinaryLogicalOperator::greaterOrEquals(), $value));
	}

	/**
	 * Creates an instance of {@link BinaryDalExpression} with `lower than` logical operator
	 * @param SqlColumn $field
	 * @param ISqlValueExpression $value value to be compared. In most cases, {@link SqlValue} is
	 * 	needed here, but expressions are allowed to (e.g., {@link SelectQuery})
	 * @return BinaryDalExpression
	 */
	static function lt(SqlColumn $field, ISqlValueExpression $value)
	{
		return new BinaryDalExpression(new BinaryExpression($field, BinaryLogicalOperator::lowerThan(), $value));
	}

	/**
	 * Creates an instance of {@link BinaryDalExpression} with `lower than or equals` logical operator
	 * @param SqlColumn $field
	 * @param ISqlValueExpression $value value to be compared. In most cases, {@link SqlValue} is
	 * 	needed here, but expressions are allowed to (e.g., {@link SelectQuery})
	 * @return BinaryDalExpression
	 */
	static function ltEq(SqlColumn $field, ISqlValueExpression $value)
	{
		return new BinaryDalExpression(new BinaryExpression($field, BinaryLogicalOperator::lowerOrEquals(), $value));
	}

	/**
	 * Creates an instnace of {@link BinaryDalExpression} with `like` logical operator
	 * @param SqlColumn $field
	 * @param ISqlValueExpression $value value to be compared. In most cases, {@link SqlValue} is
	 * 	needed here, but expressions are allowed to (e.g., {@link SelectQuery})
	 * @return BinaryDalExpression
	 */
	static function like(SqlColumn $field, ISqlValueExpression $value)
	{
		return new BinaryDalExpression(new BinaryExpression($field, BinaryLogicalOperator::like(), $value));
	}

	/**
	 * Creates an instnace of {@link BinaryDalExpression} with `like` logical operator
	 * @param SqlColumn $field
	 * @param ISqlValueExpression $value value to be compared. In most cases, {@link SqlValue} is
	 * 	needed here, but expressions are allowed to (e.g., {@link SelectQuery})
	 * @return BinaryDalExpression
	 */
	static function notLike(SqlColumn $field, ISqlValueExpression $value)
	{
		return new BinaryDalExpression(new BinaryExpression($field, BinaryLogicalOperator::notIlike(), $value));
	}

	/**
	 * Creates an instnace of {@link BinaryDalExpression} with case-insensitive `like` logical operator
	 * @param SqlColumn $field
	 * @param ISqlValueExpression $value value to be compared. In most cases, {@link SqlValue} is
	 * 	needed here, but expressions are allowed to (e.g., {@link SelectQuery})
	 * @return BinaryDalExpression
	 */
	static function ilike(SqlColumn $field, ISqlValueExpression $value)
	{
		return new BinaryDalExpression(new BinaryExpression($field, BinaryLogicalOperator::ilike(), $value));
	}

	/**
	 * Creates an instnace of {@link BinaryDalExpression} with case-insensitive `not like` logical operator
	 * @param SqlColumn $field
	 * @param ISqlValueExpression $value value to be compared. In most cases, {@link SqlValue} is
	 * 	needed here, but expressions are allowed to (e.g., {@link SelectQuery})
	 * @return BinaryDalExpression
	 */
	static function notIlike(SqlColumn $field, ISqlValueExpression $value)
	{
		return new BinaryDalExpression(new BinaryExpression($field, BinaryLogicalOperator::notIlike(), $value));
	}

	/**
	 * Creates an instnace of {@link BinaryDalExpression} with `similar to` logical operator
	 * @param SqlColumn $field
	 * @param ISqlValueExpression $value value to be compared. In most cases, {@link SqlValue} is
	 * 	needed here, but expressions are allowed to (e.g., {@link SelectQuery})
	 * @return BinaryDalExpression
	 */
	static function similar(SqlColumn $field, ISqlValueExpression $value)
	{
		return new BinaryDalExpression(new BinaryExpression($field, BinaryLogicalOperator::similarTo(), $value));
	}

	/**
	 * Creates an instnace of {@link BinaryDalExpression} with inverted `similar to` logical operator
	 * @param SqlColumn $field
	 * @param ISqlValueExpression $value value to be compared. In most cases, {@link SqlValue} is
	 * 	needed here, but expressions are allowed to (e.g., {@link SelectQuery})
	 * @return BinaryDalExpression
	 */
	static function notSimilar(SqlColumn $field, ISqlValueExpression $value)
	{
		return new BinaryDalExpression(new BinaryExpression($field, BinaryLogicalOperator::notSimilarTo(), $value));
	}

	/**
	 * Creates an instance of {@link BinaryDalExpression} with "plus" operator
	 * @param SqlColumn $field
	 * @param ISqlValueExpression $value value to be compared. In most cases, {@link SqlValue} is
	 * 	needed here, but expressions are allowed to (e.g., {@link SelectQuery})
	 * @return BinaryDalExpression
	 */
	static function add(SqlColumn $field, ISqlValueExpression $value)
	{
		return new BinaryDalExpression(new BinaryExpression($field, BinaryLogicalOperator::add(), $value));
	}

	/**
	 * Creates an instance of {@link BinaryDalExpression} with "minus" operator
	 * @param SqlColumn $field
	 * @param ISqlValueExpression $value value to be compared. In most cases, {@link SqlValue} is
	 * 	needed here, but expressions are allowed to (e.g., {@link SelectQuery})
	 * @return BinaryDalExpression
	 */
	static function sub(SqlColumn $field, ISqlValueExpression $value)
	{
		return new BinaryDalExpression(new BinaryExpression($field, BinaryLogicalOperator::substract(), $value));
	}

	/**
	 * Creates an instance of {@link BinaryDalExpression} with "multiply" operator
	 * @param SqlColumn $field
	 * @param ISqlValueExpression $value value to be compared. In most cases, {@link SqlValue} is
	 * 	needed here, but expressions are allowed to (e.g., {@link SelectQuery})
	 * @return BinaryDalExpression
	 */
	static function mul(SqlColumn $field, ISqlValueExpression $value)
	{
		return new BinaryDalExpression(new BinaryExpression($field, BinaryLogicalOperator::multiply(), $value));
	}

	/**
	 * Creates an instance of {@link BinaryDalExpression} with "division" operator
	 * @param SqlColumn $field
	 * @param ISqlValueExpression $value value to be compared. In most cases, {@link SqlValue} is
	 * 	needed here, but expressions are allowed to (e.g., {@link SelectQuery})
	 * @return BinaryDalExpression
	 */
	static function div(SqlColumn $field, ISqlValueExpression $value)
	{
		return new BinaryDalExpression(new BinaryExpression($field, BinaryLogicalOperator::divide(), $value));
	}

	/**
	 * Creates an instance of `not null` unary postfix expression
	 * @param ISqlValueExpression $subject probably, {@link SqlColumn}, but can be either
	 * 	{@link SelectQuery} or any other sql expression
	 * @return UnaryPostfixDalExpression
	 */
	static function notNull(ISqlValueExpression $subject)
	{
		return new UnaryPostfixDalExpression(new UnaryPostfixExpression($subject, UnaryPostfixLogicalOperator::isNotNull()));
	}

	/**
	 * Creates an instance of `is null` unary postfix expression
	 * @param ISqlValueExpression $subject probably, {@link SqlColumn}, but can be either
	 * 	{@link SelectQuery} or any other sql expression
	 * @return UnaryPostfixDalExpression
	 */
	static function isNull(ISqlValueExpression $subject)
	{
		return new UnaryPostfixDalExpression(new UnaryPostfixExpression($subject, UnaryPostfixLogicalOperator::isNull()));
	}

	/**
	 * Creates an instance of `is true` unary postfix expression
	 * @param ISqlValueExpression $subject probably, {@link SqlColumn}, but can be either
	 * 	{@link SelectQuery} or any other sql expression
	 * @return UnaryPostfixDalExpression
	 */
	static function isTrue(ISqlValueExpression $subject)
	{
		return new UnaryPostfixDalExpression(new UnaryPostfixExpression($subject, UnaryPostfixLogicalOperator::isTrue()));
	}

	/**
	 * Creates an instance of `is false` unary postfix expression
	 * @param ISqlValueExpression $subject probably, {@link SqlColumn}, but can be either
	 * 	{@link SelectQuery} or any other sql expression
	 * @return UnaryPostfixDalExpression
	 */
	static function isFalse(ISqlValueExpression $subject)
	{
		return new UnaryPostfixDalExpression(new UnaryPostfixExpression($subject, UnaryPostfixLogicalOperator::isFalse()));
	}

	/**
	 * Creates an instance of {@link BetweenRangeExpression}
	 * @param SqlColumn $field
	 * @param ISqlValueExpression $from starting value in range. In most cases, {@link SqlValue} is
	 * 	needed here, but expressions are allowed to (e.g., {@link SelectQuery})
	 * @param ISqlValueExpression $to ending value in range. In most cases, {@link SqlValue} is
	 * 	needed here, but expressions are allowed to (e.g., {@link SelectQuery})
	 * @return BetweenRangeDalExpression
	 */
	static function between(SqlColumn $field, ISqlValueExpression $from, ISqlValueExpression $to)
	{
		return new BetweenRangeDalExpression(new BetweenRangeExpression($field, $from, $to));
	}

	/**
	 * Helper method to check whether the field value is in the specified set of values. Strict
	 * equality is used in comparison
	 * @param SqlColumn $field
	 * @param ISqlValueExpression $set representing a set of values, In most cases,
	 * 	{@link SqlValueList} is needed here, but can also be used other Sql-compatible
	 * 	expressions, like {@link SelectQuery}
	 * @return InSetDalExpression
	 */
	static function in(SqlColumn $field, ISqlValueExpression $set)
	{
		return new InSetDalExpression(new InSetExpression($field, $set, InSetLogicalOperator::in()));
	}

	/**
	 * Helper method to check whether the field value is in the specified set of values. Strict
	 * equality is used in comparison
	 * @param SqlColumn $field
	 * @param ISqlValueExpression $set representing a set of values, In most cases,
	 * 	{@link SqlValueList} is needed here, but can also be used other Sql-compatible
	 * 	expressions, like {@link SelectQuery}
	 * @return InSetDalExpression
	 */
	static function notIn(SqlColumn $field, ISqlValueExpression $set)
	{
		return new InSetDalExpression(new InSetExpression($field, $set, InSetLogicalOperator::notIn()));
	}

	/**
	 * Creates a block of {@link IDalExpression} arguments joined with `OR` logical operator and
	 * wrapped by {@link ExpressionChain}
	 * @param IDalExpression ...
	 * @return DalExpressionChain
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
	 * Creates a block of {@link IDalExpression} arguments joined with `AND` logical operator and
	 * wrapped by {@link ExpressionChain}
	 * @param IDalExpression ...
	 * @return DalExpressionChain
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
	 * @return DalExpressionChain
	 */
	static function orChain()
	{
		return new DalExpressionChain(ExpressionChainLogicalOperator::conditionOr());
	}

	/**
	 * Creates an instance of {@link ExpressionChain} with `AND` logical operator
	 * @return DalExpressionChain
	 */
	static function andChain()
	{
		return new DalExpressionChain(ExpressionChainLogicalOperator::conditionAnd());
	}

	/**
	 * Creates an instance of {@link PrefixUnaryExpression} with prefixed "NOT" logical operator to
	 * invert the value
	 * @param ISqlValueExpression $subject probably, {@link SqlColumn}, but can be either
	 * 	{@link SelectQuery} or any other sql expression
	 * @return PrefixUnaryDalExpression
	 */
	static function not(ISqlValueExpression $subject)
	{
		return new PrefixUnaryDalExpression(new PrefixUnaryExpression(PrefixUnaryLogicalOperator::not(), $subject));
	}

	/**
	 * Creates an instance of {@link PrefixUnaryExpression} with prefixed "MINUS" logical operator to
	 * treat the field value as negative
	 * @param ISqlValueExpression $subject probably, {@link SqlColumn}, but can be either
	 * 	{@link SelectQuery} or any other sql expression
	 * @return PrefixUnaryDalExpression
	 */
	static function negative(ISqlValueExpression $subject)
	{
		return new PrefixUnaryDalExpression(new PrefixUnaryExpression(PrefixUnaryLogicalOperator::minus(), $subject));
	}
}

?>