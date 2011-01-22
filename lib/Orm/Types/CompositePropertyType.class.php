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
 * Implements a composite type handler
 *
 * Example:
 *
 * XML defintion:
 *
 * @code
 *
 *		<entity name="Address" has-dao="false">
 *			<properties>
 *				<property name="street" type="String"/>
 *				<property name="building" type="String"/>
 *			</properties>
 *		</entity>
 *
 *		<entity name="Location" has-dao="false">
 *			<properties>
 *				<property name="county" type="String"/>
 *				<property name="localAddress" type="Address"/>
 *			</properties>
 *		</entity>
 *
 *		<entity name="User" has-dao="true">
 *			<properties>
 *				<identifier />
 *				<property name="name" type="String" />
 *				<property name="location" type="Location" />
 *			</properties>
 * 		</entity>
 * @endcode
 *
 * Querying:
 *
 * @code
 * $query =
 * 	User::query()
 * 		->where(
 * 			Expression::eq('location.localAddress.street', 'WallStreet')
 * 		)
 * 		->setLimit(1);
 * $entity = $query->getEntity();
 * @endcode
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
	 * @param IMappable $entity entity to handle composite property
	 */
	function __construct(IMappable $entity)
	{
		$this->entity = $entity;
		$this->entityClass = $this->entity->getLogicalSchema()->getEntityName();
	}

	/**
	 * @return IMappable
	 */
	function getEntity()
	{
		return $this->entity;
	}

	/**
	 * Gets the virtual property.
	 *
	 * This property is built in context of the owning property but contains inner info about
	 * the composite property.
	 *
	 * This is needed because property type do not know about the database columns used to store
	 * the value
	 *
	 * @param string $name name of the property to get from the composite type
	 * @param OrmProperty $owner a property which owns the CompositePropertyType
	 * @return OrmProperty
	 */
	private function getVirtualProperty($name, OrmProperty $owner)
	{
		$idx = 0;
		$found = false;
		foreach ($this->entity->getLogicalSchema()->getProperties() as $property) {
			if ($property->getName() == $name) {
				$found = true;
				break;
			}

			$idx += $property->getType()->getColumnCount();
		}

		if (!$found) {
			throw new OrmModelIntegrityException('property not found');
		}

		return new OrmProperty(
			$name,
			array_slice($owner->getFields(), $idx, $property->getType()->getColumnCount()),
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
	
	function getEntityProperty(EntityPropertyPath $path, OrmProperty $owner) 
	{
		Assert::isFalse(
			$path->isEmpty(),
			'incomplete PropertyPath %s: %s is Composite and cannot be the tail',
			$path->getFullPath(),
			$path->getCurrentPath()
		);
		
		$vOwner = $this->getVirtualProperty($path->getNextChunk(), $owner);
		
		return $vOwner->getEntityProperty($path->peek());
	}

	protected function getCtorArgumentsPhpCode()
	{
		return array(
			$this->entityClass . '::orm()',
		);
	}
}

?>