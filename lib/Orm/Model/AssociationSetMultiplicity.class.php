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
 * Multiplicity of the association. This is bogus stratification, due Orm framework supports
 * only * containment, other types should be implemented via triggers at database schema level
 * @ingroup OrmModel
 */
final class AssociationSetMultiplicity extends Enumeration
{
	const ONE_OR_MORE = '+';
	const ZERO_OR_MORE = '*';

	/**
	 * @return AssociationSetMultiplicity
	 */
	static function oneOrMore()
	{
		return new self (self::ONE_OR_MORE);
	}

	/**
	 * @return AssociationSetMultiplicity
	 */
	static function zeroOrMore()
	{
		return new self (self::ZERO_OR_MORE);
	}
}

?>