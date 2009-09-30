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
 * Represents a predicate used in expression chaining
 * @ingroup ExpressionPredicates
 */
final class ExpressionChainPredicate extends Predicate
{
	const CONDITION_AND = 'AND';
	const CONDITION_OR = 'OR';

	/**
	 * Creates an instance of {@link ExpressionChainPredicate}
	 * @param string $id one of the class constants
	 * @return ExpressionChainPredicate
	 */
	static function create($id)
	{
		return new self($id);
	}

	/**
	 * Creates an instance of {@link ExpressionChainPredicate} with
	 * {@link ExpressionChainPredicate::CONDITION_AND} chain joiner
	 * @return ExpressionChainPredicate
	 */
	static function conditionAnd()
	{
		return self::create(self::CONDITION_AND);
	}

	/**
	 * Creates an instance of {@link ExpressionChainPredicate} with
	 * {@link ExpressionChainPredicate::CONDITION_OR} chain joiner
	 * @return ExpressionChainPredicate
	 */
	static function conditionOr()
	{
		return self::create(self::CONDITION_OR);
	}

}

?>