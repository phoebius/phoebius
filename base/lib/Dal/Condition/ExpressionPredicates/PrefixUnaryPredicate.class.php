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
 * Represents a prefix unary predicate
 * @ingroup ExpressionPredicates
 */
final class PrefixUnaryPredicate extends Predicate
{
	const NOT = 'NOT';
	const MINUS	= '-';

	/**
	 * Creates an instance of {@link PrefixUnaryPredicate}
	 * @param string $id one of the class constants
	 * @return PrefixUnaryPredicate
	 */
	static function create($id)
	{
		return new self($id);
	}

	/**
	 * Creates an instance of {@link PrefixUnaryPredicate} with {@link PrefixUnaryPredicate::NOT}
	 * value
	 * @return PrefixUnaryPredicate
	 */
	static function not()
	{
		return self::create(self::NOT);
	}

	/**
	 * Creates an instance of {@link PrefixUnaryPredicate} with {@link PrefixUnaryPredicate::MINUS}
	 * value
	 * @return PrefixUnaryPredicate
	 */
	static function minus()
	{
		return self::create(self::MINUS);
	}

}

?>