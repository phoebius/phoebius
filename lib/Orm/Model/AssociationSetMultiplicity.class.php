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
 * Multiplicity of the association. This is bogus stratification, due Orm framework supports
 * only * containment, other types should be implemented via triggers at database schema level
 * @ingroup Orm_Model
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