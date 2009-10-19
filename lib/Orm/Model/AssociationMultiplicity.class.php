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
 * Multiplicity of direct association
 * @ingroup Orm_Model
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