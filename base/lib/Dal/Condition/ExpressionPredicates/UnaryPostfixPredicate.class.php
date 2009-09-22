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
 * Represents a predicate used in unary prefix expression
 * @ingroup ExpressionPredicates
 */
final class UnaryPostfixPredicate extends Predicate
{
	const IS_NULL = 'IS NULL';
	const IS_NOT_NULL = 'IS NOT NULL';

	const IS_TRUE = 'IS TRUE';
	const IS_FALSE = 'IS FALSE';

	/**
	 * Creates an instance of {@link UnaryPostfixPredicate}
	 * @param string $id one of the class constants
	 * @return UnaryPostfixPredicate
	 */
	static function create($id)
	{
		return new self($id);
	}

	/**
	 * Creates an instance of {@link UnaryPostfixPredicate} with
	 * {@link UnaryPostfixPredicate::IS_NULL} value
	 * @return UnaryPostfixPredicate
	 */
	static function isNull()
	{
		return self::create(self::IS_NULL);
	}

	/**
	 * Creates an instance of {@link UnaryPostfixPredicate} with
	 * {@link UnaryPostfixPredicate::IS_NOT_NULL} value
	 * @return UnaryPostfixPredicate
	 */
	static function isNotNull()
	{
		return self::create(self::IS_NOT_NULL);
	}

	/**
	 * Creates an instance of {@link UnaryPostfixPredicate} with
	 * {@link UnaryPostfixPredicate::IS_TRUE} value
	 * @return UnaryPostfixPredicate
	 */
	static function isTrue()
	{
		return self::create(self::IS_TRUE);
	}

	/**
	 * Creates an instance of {@link UnaryPostfixPredicate} with
	 * {@link UnaryPostfixPredicate::IS_FALSE} value
	 * @return UnaryPostfixPredicate
	 */
	static function isFalse()
	{
		return self::create(self::IS_FALSE);
	}

}

?>