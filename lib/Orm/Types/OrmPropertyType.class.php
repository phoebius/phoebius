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
 * Represents a base type of ORM-related entity's property
 *
 * @ingroup Orm_Types
 */
abstract class OrmPropertyType
{
	/**
	 * Gets the name of the class this type implements (if any).
	 *
	 * @return string|null
	 */
	abstract function getImplClass();

	/**
	 * Converts the tuple of raw database values to the object that encapsulated ORM-related
	 * entity's property
	 *
	 * @param array $tuple set of database values, wrt OrmPropertyType::getSqlTypes()
	 * @param FetchStrategy $fetchStrategy fetch strategy to use
	 *
	 * @return mixed native value
	 */
	abstract function assemble(array $tuple, FetchStrategy $fetchStrategy);

	/**
	 * Converts the object that encapsulated ORM-related entity's property to the set of
	 * database values
	 *
	 * @param mixed $value PHP value
	 *
	 * @return array
	 */
	abstract function disassemble($value);

	/**
	 * Returns an array of ISqlType for the property
	 * @return array of key=>ISqlType
	 */
	abstract function getSqlTypes();

	/**
	 * Gets the number of database columns acquired by the property type
	 * @return integer
	 */
	abstract function getColumnCount();

	/**
	 * Converts the set  tuple of raw database values to the object that encapsulated ORM-related
	 * entity's property
	 *
	 * This method can be overridden when custom set conversion can be implemented by the type
	 * optimistically
	 *
	 * @param array $tuples
	 * @param FetchStrategy
	 * @return array of native values
	 */
	function assebmleSet(array $tuples, FetchStrategy $fetchStrategy)
	{
		$yield = array();

		foreach ($tuples as $tuple) {
			$yield = $this->assemble($tuple, $fetchStrategy);
		}

		return $yield;
	}

	/**
	 * Gets the PHP code that constructs the object with the current state
	 *
	 * @return string
	 */
	function toPhpCodeCall()
	{
		return join('', array(
			'new ',
			get_class($this),
			'(',
				join(', ', $this->getCtorArgumentsPhpCode()),
			')'
		));
	}

	/**
	 * Create a php code which implements a getter within the entity class
	 * @param IMappable $entity
	 * @param OrmProperty $property
	 */
	function toGetter(IMappable $entity, OrmProperty $property)
	{
		$returnValue =
			($implClass = $this->getImplClass())
				? $implClass
				: 'mixed';
		if ($property->getMultiplicity()->isNullable()) {
			$returnValue .= '|null';
		}

		$propertyName = $property->getName();
		$capitalizedPropertyName = ucfirst($propertyName);

		return <<<EOT
	/**
	 * @return {$returnValue}
	 */
	function get{$capitalizedPropertyName}()
	{
		return \$this->{$propertyName};
	}
EOT;
	}

	/**
	 * Create a php code which implements a setter within the entity class
	 * @param IMappable $entity
	 * @param OrmProperty $property
	 */
	function toSetter(IMappable $entity, OrmProperty $property)
	{
		$argCastType =
			($argCastType = $this->getImplClass())
				? $argCastType . ' '
				: '';

		$argDocType =
			$argCastType
				? $this->getImplClass()
				: 'scalar';

		$defaultValue =
			$property->getMultiplicity()->isNullable()
				? ' = null'
				: '';

		$propertyName = $property->getName();
		$capitalizedPropertyName = ucfirst($propertyName);

		return <<<EOT
	/**
	 * @param {$argDocType} \${$propertyName}
	 * @return {$entity->getLogicalSchema()->getEntityName()} itself
	 */
	function {$property->getSetter()}({$argCastType}\${$propertyName}{$defaultValue})
	{
		\$this->{$propertyName} = \${$propertyName};

		return \$this;
	}
EOT;
	}


	/**
	 * Create a php code which implements a field within the entity class
	 * @param IMappable $entity
	 * @param OrmProperty $property
	 */
	function toField(IMappable $entity, OrmProperty $property)
	{
		$typeImpl =
			($typeImpl = $this->getImplClass())
				? $typeImpl
				: 'scalar';

		if ($property->getMultiplicity()->isNullable()) {
			$typeImpl .= '|null';
		}

		return <<<EOT
	/**
	 * @var {$typeImpl}
	 */
	protected \${$property->getName()};
EOT;
	}

	/**
	 * A list of PHP code that is passed as the arguments to the constructor when
	 * bootstrapping the current state
	 * @return array
	 */
	protected function getCtorArgumentsPhpCode()
	{
		return array();
	}
}

?>