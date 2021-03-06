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
 * Represents a property type for container associations
 *
 * @ingroup Orm_Types
 */
abstract class ContainerPropertyType extends OrmPropertyType
{
	/**
	 * @var IQueryable
	 */
	private $container;

	/**
	 * @var IQueryable
	 */
	private $encapsulant;

	/**
	 * @param IQueryable $container
	 * @param IQueryable $encapsulant
	 */
	function __construct(
			IQueryable $container,
			IQueryable $encapsulant
		)
	{
		$this->container = $container;
		$this->encapsulant = $encapsulant;
	}

	function getContainerClassName(OrmProperty $property)
	{
		return
			$this->container->getLogicalSchema()->getEntityName()
			. ucfirst($property->getName())
			. 'Container';
	}

	function getAutoContainerClassName(OrmProperty $property)
	{
		return 'Auto' . $this->getContainerClassName($property);
	}

	final function getSqlTypes()
	{
		return array ();
	}

	final function getColumnCount()
	{
		return 0;
	}

	final function getImplClass()
	{
		return null;
	}

	final function assemble(array $tuple, FetchStrategy $fetchStrategy)
	{
		Assert::isUnreachable('%s cannot be used for readonly property', __METHOD__);
	}

	final function assebmleSet(array $tuples, FetchStrategy $fetchStrategy)
	{
		Assert::isUnreachable('%s cannot be used for readonly property', __METHOD__);
	}

	final function disassemble($value)
	{
		return array();
	}

	/**
	 * @return IQueryable
	 */
	function getEncapsulant()
	{
		return $this->encapsulant;
	}

	/**
	 * @return IQueryable
	 */
	function getContainer()
	{
		return $this->container;
	}

	function toGetter(IMappable $entity, OrmProperty $property)
	{
		$capitalizedPropertyName = ucfirst($property->getName());
		$class = $this->getContainerClassName($property);

		return <<<EOT
	/**
	 * @return {$class}
	 */
	function get{$capitalizedPropertyName}(\$readOnly = false)
	{
		return new {$class}(\$this, \$readOnly);
	}
EOT;
	}

	function toSetter(IMappable $entity, OrmProperty $property)
	{
		Assert::isUnreachable();
	}

	function toField(IMappable $entity, OrmProperty $property)
	{
		return null;
	}
}

?>