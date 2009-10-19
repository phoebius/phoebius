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
 * @ingroup Orm_Model
 */
final class AssociationBreakAction extends Enumeration implements ISqlCastable
{
	const CASCADE = 'CASCADE';
	const RESTRICT = 'RESTRICT';
	const REMOVE_ASSOCIATION = 'SET NULL';

	/**
	 * @return AssociationBreakAction
	 */
	static function cascade()
	{
		return new self (self::CASCADE);
	}

	/**
	 * @return AssociationBreakAction
	 */
	static function restrict()
	{
		return new self (self::RESTRICT);
	}

	/**
	 * @return AssociationBreakAction
	 */
	static function removeAssociation()
	{
		return new self (self::REMOVE_ASSOCIATION);
	}

	/**
	 * Casts an object to the SQL dialect string
	 * @return string
	 */
	function toDialectString(IDialect $dialect)
	{
		return $this->getValue();
	}
}

?>