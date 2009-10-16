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
 * Represents a logical operator for {@link InSetExpression} to search fields in set of values
 * @ingroup ExpressionLogicalOperators
 */
final class InSetLogicalOperator extends LogicalOperator
{
	const IN = 'IN';
	const NOT_IN = 'NOT IN';

	/**
	 * Creates an instance of {@link InSetLogicalOperator}
	 * @param string $id one of the class constants
	 * @return InSetLogicalOperator
	 */
	static function create($id)
	{
		return new self($id);
	}

	/**
	 * Creates an instance of {@link InSetLogicalOperator} with
	 * {@link InSetLogicalOperator::IN} value
	 * @return InSetLogicalOperator
	 */
	static function in()
	{
		return self::create(self::IN);
	}

	/**
	 * Creates an instance of {@link InSetLogicalOperator} with
	 * {@link InSetLogicalOperator::NOT_IN} value
	 * @return InSetLogicalOperator
	 */
	static function notIn()
	{
		return self::create(self::NOT_IN);
	}
}

?>