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
 * Represents a base implementation of ORM-related entity mapper
 *
 * @ingroup Orm
 */
final class OrmMap implements IOrmEntityBatchMapper
{
	/**
	 * @var ILogicallySchematic
	 */
	private $logicalSchema;

	/**
	 * @param ILogicallySchematic $logicalSchema all we need to know is logical structure of ORM entity
	 */
	function __construct(ILogicallySchematic $logicalSchema)
	{
		$this->logicalSchema = $logicalSchema;
	}

	function disassemble(OrmEntity $entity)
	{
		$tuple = array();

		foreach ($this->logicalSchema->getProperties() as $property) {
			if (!$property->getVisibility()->isGettable()) {
				continue;
			}

			$value = $entity->{$property->getGetter()}();

			if (is_null($value)) {
				if ($property->isIdentifier()) {
					continue;
				}

				if ($property->getMultiplicity()->is(AssociationMultiplicity::ZERO_OR_ONE)) {
					continue;
				}

				Assert::isUnreachable(
					'%s::%s cannot be valueless',
					get_class($entity),
					$property->getName()
				);
			}

			$fields = array_combine(
				$property->getFields(),
				$property->getType()->disassemble($value)
			);
			foreach ($fields as $field => $value) {
				$tuple[$field] = $value;
			}
		}

		return $tuple;
	}

	function assemble(OrmEntity $entity, array $tuple, FetchStrategy $fetchStrategy)
	{
		foreach ($this->logicalSchema->getProperties() as $property) {
			if (!$property->getVisibility()->isSettable()) {
				continue;
			}

			$propertyTuple = array();
			foreach ($property->getFields() as $field) {
				$propertyTuple[] = $tuple[$field];
			}

			$propertyType = $property->getType();
			$propertyTuple = array_combine(
				array_keys($propertyType->getSqlTypes()),
				$propertyTuple
			);

			$setter = $property->getSetter();
			$value = $propertyType->assemble($propertyTuple, $fetchStrategy);
			$entity->$setter($value);
		}

		return $entity;
	}

	function getBatchMapper()
	{
		return $this;
	}

	function finish()
	{
		// nothing
	}
}

?>