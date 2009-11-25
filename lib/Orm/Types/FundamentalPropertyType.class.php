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
final class FundamentalPropertyType
	extends PrimitivePropertyType
	implements IOrmPropertyReferencable, IOrmEntityIdGenerator
{
	/**
	 * @var DBType
	 */
	private $type;

	function __construct(DBType $type)
	{
		$this->type = $type;

		parent::__construct($type, $type->isNullable());
	}

	function getReferenceType(AssociationMultiplicity $multiplicity)
	{
		$type = clone $this->type;

		if ($type->canBeGenerated()) {
			$type->setGenerated(false);
		}

		$type->setIsNullable(
			$multiplicity->is(AssociationMultiplicity::ZERO_OR_ONE)
		);

		return new self ($type);
	}

	function getIdGenerator(IdentifiableOrmEntity $entity)
	{
		Assert::isTrue(
			$this->type->isGenerated()
		);

		$orm = call_user_func(array(get_class($entity), 'orm'));
		$identifier = $orm->getLogicalSchema()->getIdentifier();

		Assert::isTrue(
			$identifier->getType() === $this,
			'plz pass the corresponding entity'
		);

		$fields = $identifier->getFields();

		// *Important*
		// We use dirty hack to obtain RdbmsDao because IOrmEntityAccessor
		// does not provide (and should not provide!) an API to access the database
		// because IOrmEntityAccessor is a limited interface to an abstract storage.
		// In most cases this type would be used for databases so we can use this hack here.
		$dao = call_user_func(array(get_class($entity), 'dao'));
		Assert::isTrue($dao instanceof RdbmsDao);

		$db = $dao->getDB();

		$generator = $db->getGenerator(
			$orm->getPhysicalSchema()->getTable(),
			reset ($fields)
		);

		return new PropertyValueGenerator($this, $generator);
	}

	function assemble(DBValueArray $values, FetchStrategy $fetchStrategy)
	{
		$value = parent::assemble($values, $fetchStrategy);

		if (!is_null($value)) {
			if ($this->type->is(DBType::BOOLEAN)) {
				// Assume that most dbs represent their booleans from this set of values
				// so no matter to provide a separate PropertyType that handles boolean value.
				$value = in_array(
					strtolower($value),
					array('t', 'true', '1')
				);
			}
		}

		return $value;
	}

	protected function getCtorArgumentsPhpCode()
	{
		return array(
			$this->type->toPhpCodeCall()
		);
	}
}

?>