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
 * @ingroup Orm_Model
 */
class OrmClass implements IPhysicallySchematic, ILogicallySchematic, IQueryable
{
	/**
	 * @var string
	 */
	private $name;

	/**
	 * @var array of {@link OrmProperty}
	 */
    private $properties = array();

	/**
	 * @var OrmProperty|null
	 */
	private $identifier;

	/**
	 * @var IEntityMapper
	 */
	private $map;

	/**
	 * @var IOrmEntityAccessor
	 */
	private $dao;

	/**
	 * @var boolean
	 */
	private $hasDao = true;

	/**
	 * @var string
	 */
	private $dbSchema;

	/**
	 * @var string
	 */
	private $dbTableName;

	/**
	 * @return OrmEntity
	 */
	function getNewEntity()
	{
		return new $this->name;
	}

	function __sleep()
	{
		return array (
			'name', 'properties', 'identifier', 'hasDao', 'dbSchema'
		);
	}

	/**
	 * @return IOrmEntityAccessor
	 */
	function getDao()
	{
		Assert::isTrue(
			$this->hasDao,
			'%s is dao-less entity',
			$this->name
		);

		if (!$this->dao) {
			$this->dao = new RdbmsDao(
				$this->dbSchema
					? DBPool::get($this->dbSchema)
					: DBPool::getDefault(),
				$this
			);
		}

		return $this->dao;
	}

	/**
	 * @return OrmClass
	 */
	function setHasDao($flag)
	{
		Assert::isBoolean($flag);

		$this->hasDao = $flag;

		return $this;
	}

	/**
	 * @return boolean
	 */
	function hasDao()
	{
		return $this->hasDao;
	}

	/**
	 * @return boolean
	 */
	function hasNoDao()
	{
		return !$this->hasDao;
	}

	/**
	 * @return OrmClass
	 */
	function setDbSchema($dbSchema = null)
	{
		Assert::isScalarOrNull($dbSchema);

		$this->dbSchema = $dbSchema;

		return $this;
	}

	/**
	 * @return IOrmEntityMapper
	 */
	function getMap()
	{
		if (!$this->map) {
			$this->map = new OrmMap($this);
		}

		return $this->map;
	}

	/**
	 * @param string $name
	 * @return OrmClass
	 */
	function setName($name)
	{
		Assert::isScalar($name);

		$this->name = $name;

		if (!$this->dbTableName) {
			$this->setDBTableName(
				strtolower(
					preg_replace(
						'/([a-z])([A-Z])/',
						'$1_$2',
						$this->name
					)
				)
			);
		}

		return $this;
	}

	/**
	 * @return string
	 */
	function getName()
	{
		return $this->name;
	}

	/**
	 * @return OrmClass
	 */
	function dropProperties()
	{
		$this->properties = array();
		$this->identifier = null;

		return $this;
	}

	/**
	 * @return array of {@link OrmProperty}
	 */
	function getProperties()
	{
		return $this->properties;
	}

	/**
	 * @return OrmClass
	 */
	function addProperties(array $properties)
	{
		foreach ($properties as $property) {
			$this->addProperty($property);
		}

		return $this;
	}

	/**
	 * @return OrmClass
	 */
	function setProperties(array $properties)
	{
		$this->dropProperties()->addProperties($properties);

		return $this;
	}

	/**
	 * @return OrmClass
	 */
	function addProperty(OrmProperty $property)
	{
		$name = $property->getName();

		if (isset($this->properties[$name])) {
			throw new OrmModelIntegrityException("Property {$property->getName()} already defined");
		}

		$this->properties[$name] = $property;

		return $this;
	}

	/**
	 * @return OrmClass
	 */
	function setProperty(OrmProperty $property)
	{
		$this->properties[$property->getName()] = $property;

		return $this;
	}

	/**
	 * @return array of string
	 */
	function getPropertyNames()
	{
		return array_keys($this->properties);
	}

	/**
	 * @return OrmClass
	 */
	function getProperty($name)
	{
		if (!isset($this->properties[$name])) {
			throw new OrmModelIntegrityException("Property {$name} is not defined");
		}

		return $this->properties[$name];
	}

	/**
	 * @return OrmClass
	 */
	function addIdentifier(OrmProperty $property)
	{
		if ($this->identifier) {
			throw new OrmModelIntegrityException("Identifier already set");
		}

		$this->setIdentifier($property);

		return $this;
	}

	/**
	 * @return boolean
	 */
	function hasIdentifier()
	{
		return !!$this->identifier;
	}

	/**
	 * @return OrmProperty|null
	 */
	function getIdentifier()
	{
		return $this->identifier;
	}

	/**
	 * @return OrmClass
	 */
	function setIdentifier(OrmProperty $property)
	{
		$this->addProperty($property);
		$this->identifier = $property;

		return $this;
	}

	/**
	 * @return OrmClass
	 */
	function dropIdentifier()
	{
		if ($this->identifier) {
			unset($this->properties[$this->identifier->getName()]);
		}

		$this->identifier = null;

		return $this;
	}

	/**
	 * @return ILogicallySchematic
	 */
	function getLogicalSchema()
	{
		return $this;
	}

	/**
	 * @return IPhysicallySchematic
	 */
	function getPhysicalSchema()
	{
		return $this;
	}

	/**
	 * @return OrmClass
	 */
	function setDBTableName($dbTableName)
	{
		Assert::isScalar($dbTableName);

		$this->dbTableName = $dbTableName;

		return $this;
	}

	/**
	 * Gets the name of the DB table where entities are stored
	 * @return string
	 */
	function getDBTableName()
	{
		return $this->dbTableName;
	}

	/**
	 * Returns the name of the class representing an entity
	 * @return string
	 */
	function getEntityName()
	{
		return ucfirst($this->name);
	}

	/**
	 * Array of columnName => DBType
	 * @return array
	 */
	function getDBFields()
	{
		$columns = array();

		foreach ($this->properties as $property) {
			foreach ($property->getDBFields() as $field) {
				$columns[] = $field;
			}
		}

		return $columns;
	}
}

?>
