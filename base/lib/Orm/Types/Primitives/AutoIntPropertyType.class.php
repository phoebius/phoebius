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
 * @ingroup PrimitiveOrmTypes
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