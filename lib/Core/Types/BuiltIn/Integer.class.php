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
 * Portions of code (c) Konstantin V. Arkhipov <voxus@onphp.org>
 * @ingroup Core_Types_BuiltIn
 */
class Integer extends Ranged
{
	//const SIGNED_SMALL_MIN = -32768;
	//const SIGNED_SMALL_MAX = +32767;

	const SIGNED_MIN = -2147483648;
	const SIGNED_MAX = +2147483647;

	//const SIGNED_BIG_MIN = ONPHP_HOST_INT_MIN;
	//const SIGNED_BIG_MAX = ONPHP_HOST_INT_MAX;

	//const UNSIGNED_SMALL_MAX = 65535;
	//const UNSIGNED_MAX = 4294967295;

	/**
	 * @return Integer
	 */
	static function create($value)
	{
		return new self ($value);
	}

	/**
	 * @return Integer
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
		return new IntegerPropertyType(
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
		return (int)parent::getValue();
	}

	/**
	 * @return boolean
	 */
	protected function isValidValue($value)
	{
		return
			   parent::isValidValue($value)
			&& TypeUtils::isInteger($value);
	}

	/**
	 * @return integer
	 */
	protected function getMin()
	{
		return self::SIGNED_MIN;
	}

	/**
	 * @return integer
	 */
	protected function getMax()
	{
		return self::SIGNED_MAX;
	}
}

?>