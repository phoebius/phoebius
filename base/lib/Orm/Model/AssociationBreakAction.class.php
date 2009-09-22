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
 * @ingroup OrmModel
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