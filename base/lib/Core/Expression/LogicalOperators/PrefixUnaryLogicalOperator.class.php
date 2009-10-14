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
 * Represents a prefix unary logical operator
 * @ingroup ExpressionLogicalOperators
 */
final class PrefixUnaryLogicalOperator extends LogicalOperator
{
	const NOT = 'NOT';
	const MINUS	= '-';

	/**
	 * Creates an instance of {@link PrefixUnaryLogicalOperator}
	 * @param string $id one of the class constants
	 * @return PrefixUnaryLogicalOperator
	 */
	static function create($id)
	{
		return new self($id);
	}

	/**
	 * Creates an instance of {@link PrefixUnaryLogicalOperator} with {@link PrefixUnaryLogicalOperator::NOT}
	 * value
	 * @return PrefixUnaryLogicalOperator
	 */
	static function not()
	{
		return self::create(self::NOT);
	}

	/**
	 * Creates an instance of {@link PrefixUnaryLogicalOperator} with {@link PrefixUnaryLogicalOperator::MINUS}
	 * value
	 * @return PrefixUnaryLogicalOperator
	 */
	static function minus()
	{
		return self::create(self::MINUS);
	}

}

?>