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
 * @ingroup BuiltInCoreTypes
 */
final class Float extends Decimal
{
	/**
	 * @return Float
	 */
	static function create($value)
	{
		return new self ($value);
	}

	/**
	 * @return Float
	 */
	static function cast($value)
	{
		return new self ($value);
	}

	/**
	 * @return OrmPropertyType
	 */
	static function getHandler(AssociationMultiplicity $multiplicity)
	{
		return new FloatPropertyType(
			null,
			null,
			$multiplicity->is(AssociationMultiplicity::ZERO_OR_ONE)
		);
	}

	/**
	 * @return float
	 */
	function getValue()
	{
		return (float) parent::getValue();
	}
}

?>