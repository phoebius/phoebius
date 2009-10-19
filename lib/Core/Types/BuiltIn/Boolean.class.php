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
final class Boolean extends Scalar
{
	/**
	 * @return Boolean
	 */
	static function create($value)
	{
		return new self ($value);
	}

	/**
	 * @return Boolean
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
		return new BooleanPropertyType(
			null,
			$multiplicity->is(AssociationMultiplicity::ZERO_OR_ONE)
		);
	}

	/**
	 * @return Boolean
	 */
	function setValue($value)
	{
		if (is_bool($value)) {
			parent::setValue($value);
		}
		else {
			if (in_array($value, array (1, 'true', 't'))) {
				parent::setValue(true);
			}
			else if (in_array($value, array (0, 'false', 'f'))) {
				parent::setValue(false);
			}
			else {
				throw new TypeCastException(
					Type::typeof($this),
					$value,
					'not an Boolean value specified'
				);
			}
		}

		return $this;
	}

	/**
	 * @return boolean
	 */
	protected function isValidValue($value)
	{
		return is_bool($value);
	}
}

?>