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
 * Multiplicity of direct association
 * @ingroup OrmModel
 */
final class AssociationMultiplicity extends Enumeration
{
	/**
	 * Nullable
	 */
	const ZERO_OR_ONE = 'zeroOrOne';

	/**
	 * Not-nullable
	 */
	const EXACTLY_ONE = 'exactlyOne';

	/**
	 * @return AssociationMultiplicity
	 */
	static function zeroOrOne()
	{
		return new self (self::ZERO_OR_ONE);
	}

	/**
	 * @return AssociationMultiplicity
	 */
	static function exactlyOne()
	{
		return new self (self::EXACTLY_ONE);
	}
}

?>