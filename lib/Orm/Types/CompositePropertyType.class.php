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
 * Implements a composite type handler
 *
 * @ingroup Orm_Types
 */
final class CompositePropertyType extends OrmPropertyType
{
	/**
	 * @var IMappable
	 */
	private $entity;

	/**
	 * @var string
	 */
	private $entityClass;

	/**
	 * @var array of ISqlType
	 */
	private $sqlTypes;

	/**
	 * @var array
	 */
	private $mappings;
	private $fields;

	/**
	 * @param IMappable $entity entity to handle composite property
	 */
	function __construct(IMappable $entity, array $fields = null)
	{
		$this->entity = $entity;
		$this->entityClass = $this->entity->getLogicalSchema()->getEntityName();

		if ($fields) {
			$this->importFields($fields);
		}
	}

	function importFields(array $fields)
	{
		$this->fields = $fields;

		$idx = 0;

		foreach ($this->entity->getLogicalSchema()->getProperties() as $name => $property) {
			$type = $property->getType();
			$count = $type->getColumnCount();
			$this->mappings[$name] = array_slice($fields, $idx, $count);

			if ($type instanceof self) {
				$type->importFields($this->mappings[$name]);
			}

			$idx += $count;
		}

		return $this;
	}

	/**
	 * @return IMappable
	 */
	function getEntity()
	{
		return $this->entity;
	}

	function getVirtualProperty($name)
	{
		Assert::isNotNull($this->mappings, 'mappings are not yet set');

		$property = $this->entity->getLogicalSchema()->getProperty($name);

		return new OrmProperty(
			$name,
			$this->mappings[$name],
			$property->getType(),
			$property->getVisibility(),
			$property->getMultiplicity(),
			$property->isUnique(),
			$property->isIdentifier()
		);
	}

	function getImplClass()
	{
		return $this->entityClass;
	}

	function assemble(array $tuple, FetchStrategy $fetchStrategy)
	{
		return $this->entity->getMap()->assemble(
			$this->entity->getLogicalSchema()->getNewEntity(),
			$tuple,
			$fetchStrategy
		);
	}

	function disassemble($value)
	{
		return $this->entity->getMap()->disassemble($value);
	}

	function getSqlTypes()
	{
		if (!$this->sqlTypes) {
			$this->sqlTypes = array();

			foreach ($this->entity->getLogicalSchema()->getProperties() as $property) {
				$fields = array_combine(
					$property->getFields(),
					$property->getType()->getSqlTypes()
				);

				$this->sqlTypes = array_merge($this->sqlTypes, $fields);
			}
		}

		return $this->sqlTypes;
	}

	function getColumnCount()
	{
		return count($this->getSqlTypes());
	}

	protected function getCtorArgumentsPhpCode()
	{
		if ($this->fields) {
			$fields = array();
			foreach ($this->fields as $field) {
				$fields[] = '\'' . $field . '\'';
			}

			$fields = 'array(' . join(', ', $fields).')';
		}
		else {
			$fields = 'null';
		}

		return array(
			$this->entityClass . '::orm()',
			$fields
		);
	}
}

?>