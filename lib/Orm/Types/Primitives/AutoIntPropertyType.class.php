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
final class AutoIntPropertyType
		extends IntegerPropertyType
		implements IOrmPropertyReferencable, IOrmPropertyAssignable, IOrmEntityIdGenerator
{
	/**
	 * @return AutoIntPropertyType
	 */
	static function getHandler(AssociationMultiplicity $multiplicity)
	{
		return new self;
	}

	/**
	 * @return IntegerPropertyType
	 */
	static function getRefHandler(AssociationMultiplicity $multiplicity)
	{
		return new IntegerPropertyType(
			null,
			$multiplicity->is(AssociationMultiplicity::ZERO_OR_ONE)
		);
	}

	function __construct()
	{
		parent::__construct(null, true);
	}

	function generate(IdentifiableOrmEntity $entity)
	{
		$orm = call_user_func(array(get_class($entity), 'orm'));
		$identifier = $orm->getLogicalSchema()->getIdentifier();

		Assert::isTrue(
			$identifier->getType() === $this,
			'plz pass the corresponding entity'
		);

		$fields = $identifier->getDBFields();
		$types = $this->getDBFields();

		// *Important*
		// We use dirty hack to obtain RdbmsDao because IOrmEntityAccessor
		// does not provide (and should not provide!) an API to access the database
		// because IOrmEntityAccessor is a limited interface to an abstract storage.
		// In most cases this type would be used for databases so we can use this hack here.
		$dao = call_user_func(array(get_class($entity), 'dao'));
		Assert::isTrue($dao instanceof RdbmsDao);

		$db = $dao->getDB();

		$generator = $db->getGenerator(
			$orm->getPhysicalSchema()->getDBTableName(),
			reset ($fields),
			reset ($types)
		);

		return new PropertyValueGenerator($this, $generator);
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
		return $db->preGenerate($tableName, reset($fields));
	}

	/**
	 * @return mixed
	 */
	function getGeneratedId(DB $db, $tableName, OrmProperty $ormProperty)
	{
		$fields = $ormProperty->getDBFields();
		return $db->getGeneratedId($tableName, reset($fields));
	}

	protected function getCtorArgumentsPhpCode()
	{
		return array(
		);
	}
}

?>