<?php
/* ***********************************************************************************************
 *
 * Phoebius Framework
 *
 * **********************************************************************************************
 *
 * Copyright (c) 2009 phoebius.org
 *
 * This program is free software; you can redistribute it and/or modify it under the terms
 * of the GNU Lesser General Public License as published by the Free Software Foundation;
 * either version 3 of the License, or (at your option) any later version.
 *
 * You should have received a copy of the GNU Lesser General Public License along with
 * this program; if not, see <http://www.gnu.org/licenses/>.
 *
 ************************************************************************************************/

/**
 * @ingroup Core_Types_BuiltIn
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