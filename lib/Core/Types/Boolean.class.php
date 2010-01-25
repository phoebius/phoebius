<?php
/* ***********************************************************************************************
 *
 * Phoebius Framework
 *
 * **********************************************************************************************
 *
 * Copyright (c) 2009 Scand Ltd.
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
 * Represents a box for primitive boolean
 *
 * @ingroup Core_Types
 */
final class Boolean extends Scalar
{
	static function cast($value)
	{
		return new self ($value);
	}

	static function getOrmPropertyType(AssociationMultiplicity $multiplicity)
	{
		$type = new DBType(
			DBType::BOOLEAN,
			/* is nullable */$multiplicity->isNullable(),
			/* size */null,
			/* precision */null,
			/* scale */null,
			/* is generated */false
		);

		return $type->getOrmPropertyType();
	}

	function setValue($value)
	{
		if (!is_bool($value)) {
			if (in_array($value, array (1, 'true', 't'))) {
				$value = true;
			}
			else if (in_array($value, array (0, 'false', 'f'))) {
				$value = false;
			}
			else {
				throw new TypeCastException(
					$this,
					$value,
					'not an Boolean value specified'
				);
			}
		}

		parent::setValue($value);

		return $this;
	}

	protected function isValidValue($value)
	{
		return is_bool($value);
	}
}

?>