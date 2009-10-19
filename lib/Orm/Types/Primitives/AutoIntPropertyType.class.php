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
 * @ingroup Orm_Types
 */
final class AutoIntPropertyType extends IntegerPropertyType implements IHandled, IReferenced, IGenerated
{
	/**
	 * @return AutoIntPropertyType
	 */
	static function getHandler(AssociationMultiplicity $multiplicity)
	{
		return new self (
			$multiplicity->is(AssociationMultiplicity::ZERO_OR_ONE)
		);
	}

	/**
	 * @return IntegerPropertyType
	 */
	static function getRefHandler(AssociationMultiplicity $multiplicity)
	{
		return new IntegerPropertyType(
			null,
			null,
			$multiplicity->is(AssociationMultiplicity::ZERO_OR_ONE)
		);
	}

	function __construct($isNullable)
	{
		parent::__construct(null, null, $isNullable);
	}

	/**
	 * @return array
	 */
	function getDBFields()
	{
		return array (
			DBType::create(DBType::INTEGER)
				->setGenerated(true)
				->setIsNullable($this->isNullable())
		);
	}

	/**
	 * @return void
	 */
	function preGenerate(DB $db, $tableName, OrmProperty $ormProperty)
	{
		$fields = $ormProperty->getDBFields();
		reset($fields);
		return $db->preGenerate($tableName, key($fields));
	}

	/**
	 * @return mixed
	 */
	function getGeneratedId(DB $db, $tableName, OrmProperty $ormProperty)
	{
		$fields = $ormProperty->getDBFields();
		reset($fields);
		return $db->getGeneratedId($tableName, key($fields));
	}

	protected function getCtorArgumentsPhpCode()
	{
		return array(
			$this->isNullable()
				? 'true'
				: 'false'
		);
	}
}

?>