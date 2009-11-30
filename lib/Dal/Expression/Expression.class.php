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
 * Helper class to create expression nodes (aka IExpression) of various types.
 *
 * Examples (and the produced SQL code):
 * @code
 * // "id" = 1
 * Expression::eq("id", 1);
 *
 * // "id" IS NOT NULL
 * Expression::notNull("id");
 *
 * @code
 * // "type" IN ("completed", "pending")
 * Expression::in("type", array("completed", "pending"));
 *
 * // "id" IN (1, 2) OR "id" IS NULL
 * Expression::disjunction(
 * 	Expression::inSet("id", array(1, 2),
 * 	Expression::isNull("id")
 * );
 *
 * // "cost" / 2 > "discount"
 * Expression::gt(
 * 	Expression::div("cost", 2),
 * 	"discount"
 * );
 *
 * // my favourite one: (checked=1) AND ( (time <2) OR (time > 10) )
 * Expression::conjunction(
 * 	Expression::eq("checked", 1),
 * 	Expression::orChain(
 * 		Expression::lt("time", 2),
 * 		Expression::gt("time", 10)
 * 	)
 * ); // now this gracefully wrapped up in a bow =)
 *
 * // another one: WHERE (active = 1) AND (name LIKE '%alex%' OR email LIKE '%alex%')
 * Expression::conjunction(
 * 	Expression::eq("active", 1),
 * 	Expression::disjunction(
 * 		Expression::like("name", "%alex%"),
 * 		Expression::like("email", "%alex%")
 * 	)
 * );
 * @endcode
 *
 * @ingroup Dal_Expression
 */
final class Expression extends StaticClass
{
	/**
	 * Creates an instance of binary expression node, representing the construction: subject = value
	 *
	 * @param mixed $subject logical subject
	 * @param mixed $value value to match the subject
	 *
	 * SQL example:
	 * @code
	 * // "id" = 1
	 * Expression::eq("id", 1);
	 * @endcode
	 * @return BinaryExpression
	 */
	static function eq($subject, $value)
	{
		return new BinaryExpression($subject, BinaryLogicalOperator::eq(), $value);
	}

	/**
	 * Creates an instance of binary expression node, representing the construction: subject != value
	 *
	 * @param mixed $subject logical subject
	 * @param mixed $value value to match the subject
	 *
	 * SQL example:
	 * @code
	 * // "id" != 1
	 * Expression::neq("id", 1);
	 * @endcode
	 * @return BinaryExpression
	 */
	static function neq($subject, $value)
	{
		return new BinaryExpression($subject, BinaryLogicalOperator::notEquals(), $value);
	}

	/**
	 * Creates an instance of binary expression node, representing the construction: subject > value
	 *
	 * @param mixed $subject logical subject
	 * @param mixed $value value to match the subject
	 *
	 * SQL example:
	 * @code
	 * // "id" > 1
	 * Expression::gt("id", 1);
	 * @endcode
	 *
	 * @return BinaryExpression
	 */
	static function gt($subject, $value)
	{
		return new BinaryExpression($subject, BinaryLogicalOperator::greaterThan(), $value);
	}

	/**
	 * Creates an instance of binary expression node, representing the construction: subject >= value
	 *
	 * @param mixed $subject logical subject
	 * @param mixed $value value to match the subject
	 *
	 * SQL example:
	 * @code
	 * // "id" >= 1
	 * Expression::gtEq("id", 1);
	 * @endcode
	 *
	 * @return BinaryExpression
	 */
	static function gtEq($subject, $value)
	{
		return new BinaryExpression($subject, BinaryLogicalOperator::greaterOrEquals(), $value);
	}

	/**
	 * Creates an instance of binary expression node, representing the construction: subject < value
	 *
	 * @param mixed $subject logical subject
	 * @param mixed $value value to match the subject
	 *
	 * SQL example:
	 * @code
	 * // "id" < 1
	 * Expression::lt("id", 1);
	 * @endcode
	 * @return BinaryExpression
	 */
	static function lt($subject, $value)
	{
		return new BinaryExpression($subject, BinaryLogicalOperator::lowerThan(), $value);
	}

	/**
	 * Creates an instance of binary expression node, representing the construction: subject <= value
	 *
	 * @param mixed $subject logical subject
	 * @param mixed $value value to match the subject
	 *
	 * SQL example:
	 * @code
	 * // "id" <= 1
	 * Expression::ltEq("id", 1);
	 * @endcode
	 * @return BinaryExpression
	 */
	static function ltEq($subject, $value)
	{
		return new BinaryExpression($subject, BinaryLogicalOperator::lowerOrEquals(), $value);
	}

	/**
	 * Creates an instance of binary expression node, representing the construction: subject LIKE value
	 *
	 * @param mixed $subject logical subject
	 * @param mixed $value value to match the subject
	 *
	 * SQL example:
	 * @code
	 * // "name" LIKE "mobi%"
	 * Expression::like("name", "mobi%");
	 * @endcode
	 * @return BinaryExpression
	 */
	static function like($subject, $value)
	{
		return new BinaryExpression($subject, BinaryLogicalOperator::like(), $value);
	}

	/**
	 * Creates an instance of binary expression node, representing the construction: subject NOT LIKE value
	 *
	 * @param mixed $subject logical subject
	 * @param mixed $value value to match the subject
	 *
	 * SQL example:
	 * @code
	 * // "name" NOT LIKE "mobi%"
	 * Expression::notLike("name", "mobi%");
	 * @endcode
	 * @return BinaryExpression
	 */
	static function notLike($subject, $value)
	{
		return new BinaryExpression($subject, BinaryLogicalOperator::notLike(), $value);
	}

	/**
	 * Creates an instance of binary expression node, representing the construction: subject ILIKE value
	 *
	 * @param mixed $subject logical subject
	 * @param mixed $value value to match the subject
	 *
	 * SQL example:
	 * @code
	 * // "name" ILIKE "mobi%"
	 * Expression::ilike("name", "mobi%");
	 * @endcode
	 * @return BinaryExpression
	 */
	static function ilike($subject, $value)
	{
		return new BinaryExpression($subject, BinaryLogicalOperator::ilike(), $value);
	}

	/**
	 * Creates an instance of binary expression node, representing the construction: subject NOT ILIKE value
	 *
	 * @param mixed $subject logical subject
	 * @param mixed $value value to match the subject
	 *
	 * SQL example:
	 * @code
	 * // "name" NOT ILIKE "mobi%"
	 * Expression::notIlike("name", "mobi%");
	 * @endcode
	 * @return BinaryExpression
	 */
	static function notIlike($subject, $value)
	{
		return new BinaryExpression($subject, BinaryLogicalOperator::notIlike(), $value);
	}

	/**
	 * Creates an instance of binary expression node, representing the construction: subject SIMILAR TO value
	 *
	 * @param mixed $subject logical subject
	 * @param mixed $value value to match the subject
	 * @return BinaryExpression
	 */
	static function similar($subject, $value)
	{
		return new BinaryExpression($subject, BinaryLogicalOperator::similarTo(), $value);
	}

	/**
	 * Creates an instance of binary expression node, representing the construction: subject NOT SIMILAR TO value
	 *
	 * @param mixed $subject logical subject
	 * @param mixed $value value to match the subject
	 * @return BinaryExpression
	 */
	static function notSimilar($subject, $value)
	{
		return new BinaryExpression($subject, BinaryLogicalOperator::notSimilarTo(), $value);
	}

	/**
	 * Creates an instance of binary expression node, representing the construction: subject + value
	 *
	 * @param mixed $subject logical subject
	 * @param mixed $value value to add to the subject
	 *
	 * SQL example:
	 * @code
	 * // "cost" + 2
	 * Expression::add("cost", 2);
	 * @endcode
	 * @return BinaryExpression
	 */
	static function add($subject, $value)
	{
		return new BinaryExpression($subject, BinaryLogicalOperator::add(), $value);
	}

	/**
	 * Creates an instance of binary expression node, representing the construction: subject - value
	 *
	 * @param mixed $subject logical subject
	 * @param mixed $value value to subtract from the subject
	 *
	 * SQL example:
	 * @code
	 * // "cost" - 2
	 * Expression::sub("cost", 2);
	 * @endcode
	 * @return BinaryExpression
	 */
	static function sub($subject, $value)
	{
		return new BinaryExpression($subject, BinaryLogicalOperator::substract(), $value);
	}

	/**
	 * Creates an instance of binary expression node, representing the construction: subject * value
	 *
	 * @param mixed $subject logical subject
	 * @param mixed $value value to multiply the subject
	 *
	 * SQL example:
	 * @code
	 * // "cost" * 2
	 * Expression::mul("cost", 2);
	 * @endcode
	 * @return BinaryExpression
	 */
	static function mul($subject, $value)
	{
		return new BinaryExpression($subject, BinaryLogicalOperator::multiply(), $value);
	}

	/**
	 * Creates an instance of binary expression node, representing the construction: subject / value
	 *
	 * @param mixed $subject logical subject
	 * @param mixed $value value to divide the subject
	 *
	 * SQL example:
	 * @code
	 * // "cost" / 2
	 * Expression::div("cost", 2);
	 * @endcode
	 * @return BinaryExpression
	 */
	static function div($subject, $value)
	{
		return new BinaryExpression($subject, BinaryLogicalOperator::divide(), $value);
	}

	/**
	 * Creates an instance of binary expression node, representing the construction: subject NOT NOT NULL
	 *
	 * @param mixed $subject logical subject
	 *
	 * SQL example:
	 * @code
	 * // "id" IS NOT NULL
	 * Expression::notNull("id");
	 * @endcode
	 * @return UnaryPostfixExpression
	 */
	static function notNull($subject)
	{
		return new UnaryPostfixExpression($subject, UnaryPostfixLogicalOperator::isNotNull());
	}

	/**
	 * Creates an instance of binary expression node, representing the construction: subject IS NULL
	 *
	 * @param mixed $subject logical subject
	 *
	 * SQL example:
	 * @code
	 * // "id" IS NULL
	 * Expression::isNull("id");
	 * @endcode
	 * @return UnaryPostfixExpression
	 */
	static function isNull($subject)
	{
		return new UnaryPostfixExpression($subject, UnaryPostfixLogicalOperator::isNull());
	}

	/**
	 * Creates an instance of binary expression node, representing the construction: subject IS TRUE
	 *
	 * @param mixed $subject logical subject
	 *
	 * SQL example:
	 * @code
	 * // "hasSmth" IS TRUE
	 * Expression::isTrue("hasSmth");
	 * @endcode
	 * @return UnaryPostfixExpression
	 */
	static function isTrue($subject)
	{
		return new UnaryPostfixExpression($subject, UnaryPostfixLogicalOperator::isTrue());
	}

	/**
	 * Creates an instance of binary expression node, representing the construction: subject IS FALSE
	 *
	 * @param mixed $subject logical subject
	 *
	 * SQL example:
	 * @code
	 * // "hasSmth" IS FALSE
	 * Expression::isFalse("hasSmth");
	 * @endcode
	 * @return UnaryPostfixExpression
	 */
	static function isFalse($subject)
	{
		return new UnaryPostfixExpression($subject, UnaryPostfixLogicalOperator::isFalse());
	}

	/**
	 * Creates an instance of binary expression node, representing the construction: subject between A and B
	 *
	 * @param mixed $subject logical subject
	 * @param mixed $from the beginning of the range
	 * @param mixed $to the end of the range
	 *
	 *
	 * SQL example:
	 * @code
	 * // "price" BETWEEN 50 AND 100
	 * Expression::between("price", 50, 100);
	 * @endcode
	 * @return BetweenRangeExpression
	 */
	static function between($subject, $from, $to)
	{
		return new BetweenRangeExpression($subject, $from, $to);
	}

	/**
	 * Creates an instance of binary expression node, representing the construction: subject in set
	 *
	 * @param mixed $subject logical subject
	 * @param array $set set of value the subject should match
	 *
	 * SQL example:
	 * @code
	 * // "type" IN ("completed", "pending")
	 * Expression::in("type", array("completed", "pending"));
	 * @endcode
	 * @return InSetExpression
	 */
	static function in($subject, array $set)
	{
		return new InSetExpression($subject, $set, InSetLogicalOperator::in());
	}

	/**
	 * Creates an instance of binary expression node, representing the construction: subject not in set
	 *
	 * @param mixed $subject logical subject
	 * @param array $set set of value the subject should match
	 *
	 * SQL example:
	 * @code
	 * // "type" NOT IN ("completed", "pending")
	 * Expression::notIn("type", array("completed", "pending"));
	 * @endcode
	 * @return InSetExpression
	 */
	static function notIn($subject, $set)
	{
		return new InSetExpression($subject, $set, InSetLogicalOperator::notIn());
	}

	/**
	 * Creates an instance of {@link PrefixUnaryExpression} with prefixed "NOT" logical operator to
	 * invert the value
	 *
	 * @param mixed $subject logical subject
	 *
	 * @return PrefixUnaryExpression
	 */
	static function not($subject)
	{
		return new PrefixUnaryExpression(PrefixUnaryLogicalOperator::not(), $subject);
	}

	/**
	 * Creates an instance of binary expression node, representing the construction: -subject
	 *
	 * @param mixed $subject logical subject
	 * @return PrefixUnaryExpression
	 */
	static function negative($subject)
	{
		return new PrefixUnaryExpression(PrefixUnaryLogicalOperator::minus(), $subject);
	}

	/**
	 * Creates the disjunction chain filling it with expressions passed separately as arguments
	 *
	 * Example:
	 * @code
	 * // "id" IN (1, 2) OR "id" IS NULL
	 * Expression::disjunction(
	 * 	Expression::inSet("id", array(1, 2),
	 * 	Expression::isNull("id")
	 * );
	 * @endcode
	 * @param IExpression ...
	 * @return ExpressionChain
	 */
	static function disjunction()
	{
		$args = func_get_args();
		$chain = self::orChain();
		foreach ($args as $arg) {
			$chain->add($arg);
		}

		return $chain;
	}

	/**
	 * Creates the conjunction chain filling it with expressions passed separately as arguments
	 *
	 * Example:
	 * @code
	 * // "id" IN (1, 2) OR "id" IS NULL
	 * Expression::conjunction(
	 * 	Expression::inSet("id", array(1, 2),
	 * 	Expression::isNull("id")
	 * );
	 * @endcode
	 * @param IExpression ...
	 * @return ExpressionChain
	 */
	static function conjunction()
	{
		$args = func_get_args();
		$chain = self::andChain();
		foreach ($args as $arg) {
			$chain->add($arg);
		}
		return $chain;
	}

	/**
	 * Disjunction chain of expressions
	 * @return ExpressionChain
	 */
	static function orChain()
	{
		return new ExpressionChain(ExpressionChainLogicalOperator::conditionOr());
	}

	/**
	 * Conjunction chain of expressions
	 * @return ExpressionChain
	 */
	static function andChain()
	{
		return new ExpressionChain(ExpressionChainLogicalOperator::conditionAnd());
	}
}

?>