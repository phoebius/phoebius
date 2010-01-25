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
 * Represens a box for any scalar
 *
 * @ingroup Core_Types
 */
class Scalar extends Primitive
{
	static function cast($value)
	{
		return new self ($value);
	}

	static function getOrmPropertyType(AssociationMultiplicity $multiplicity)
	{
		$type = new DBType(
			DBType::VARCHAR,
			/* is nullable */$multiplicity->isNullable(),
			/* size */null,
			/* precision */null,
			/* scale */null,
			/* is generated */false
		);

		return $type->getOrmPropertyType();
	}
}

?>