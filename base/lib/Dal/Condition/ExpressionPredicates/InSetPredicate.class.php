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
 * Represents a predicate for {@link InSetExpression} to search fields in set of values
 * @ingroup ExpressionPredicates
 */
final class InSetPredicate extends Predicate
{
	const IN = 'IN';
	const NOT_IN = 'NOT IN';

	/**
	 * Creates an instance of {@link InSetPredicate}
	 * @param string $id one of the class constants
	 * @return InSetPredicate
	 */
	static function create($id)
	{
		return new self($id);
	}

	/**
	 * Creates an instance of {@link InSetPredicate} with
	 * {@link InSetPredicate::IN} value
	 * @return InSetPredicate
	 */
	static function in()
	{
		return self::create(self::IN);
	}

	/**
	 * Creates an instance of {@link InSetPredicate} with
	 * {@link InSetPredicate::NOT_IN} value
	 * @return InSetPredicate
	 */
	static function notIn()
	{
		return self::create(self::NOT_IN);
	}
}

?>