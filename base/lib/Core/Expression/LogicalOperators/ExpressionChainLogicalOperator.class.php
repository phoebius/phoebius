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
 * Represents a logical operator used in expression chaining
 * @ingroup ExpressionLogicalOperators
 */
final class ExpressionChainLogicalOperator extends LogicalOperator
{
	const CONDITION_AND = 'AND';
	const CONDITION_OR = 'OR';

	/**
	 * Creates an instance of {@link ExpressionChainLogicalOperator}
	 * @param string $id one of the class constants
	 * @return ExpressionChainLogicalOperator
	 */
	static function create($id)
	{
		return new self($id);
	}

	/**
	 * Creates an instance of {@link ExpressionChainLogicalOperator} with
	 * {@link ExpressionChainLogicalOperator::CONDITION_AND} chain joiner
	 * @return ExpressionChainLogicalOperator
	 */
	static function conditionAnd()
	{
		return self::create(self::CONDITION_AND);
	}

	/**
	 * Creates an instance of {@link ExpressionChainLogicalOperator} with
	 * {@link ExpressionChainLogicalOperator::CONDITION_OR} chain joiner
	 * @return ExpressionChainLogicalOperator
	 */
	static function conditionOr()
	{
		return self::create(self::CONDITION_OR);
	}

}

?>