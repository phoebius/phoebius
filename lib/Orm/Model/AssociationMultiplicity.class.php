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
	 * Nullable (i.e. value can be not presented at all)
	 *
	 * @return AssociationMultiplicity
	 */
	static function zeroOrOne()
	{
		return new self (self::ZERO_OR_ONE);
	}

	/**
	 * Exact association (value MUST be presented)
	 *
	 * @return AssociationMultiplicity
	 */
	static function exactlyOne()
	{
		return new self (self::EXACTLY_ONE);
	}

	/**
	 * Determines whether mulitplicity allows null values
	 *
	 * @return boolean
	 */
	function isNullable()
	{
		return $this->is(self::ZERO_OR_ONE);
	}
}

?>