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
 * @ingroup OrmModel
 */
class OrmClass implements IPhysicallySchematic, ILogicallySchematic, IOrmQuery, IQueried
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
	 * @var OrmProperty
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
	 * @return OrmEntity
	 */
	function getNewEntity()
	{
		return new $this->name;
	}

	function _sleep()
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
				$this->getMap(),
				$this,
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
	 * Gets the name of the DB table where entities are stored
	 * @return string
	 */
	function getDBTableName()
	{
		return
			strtolower(
				preg_replace(
					'/([a-z])([A-Z])/',
					'$1_$2',
					$this->name
				)
			);
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
	 * @return IOrmQuery
	 */
	function getOrmQuery()
	{
		return $this;
	}

	/**
	 * $rawValue structure:
	 *  - key is dbColumn name
	 *  - value is SqlValue
	 * @return array
	 */
	function makeColumnValue(OrmProperty $property, array $rawValue)
	{
		$dbValues = array();
		foreach ($this->getDBColumnNames($property) as $columnName => $innerName) {
			$dbValues[$columnName] = $rawValue[$innerName];
		}

		return $dbValues;
	}

	/**
	 * @return array
	 */
	function makeRawValue(OrmProperty $property, $dbValues)
	{
		$rawValue = array();

		foreach ($this->getDBColumnNames($property) as $columnName => $innerName) {
			if (array_key_exists($columnName, $dbValues)) {
				$rawValue[$innerName] = $dbValues[$columnName];
			}
		}

		return $rawValue;
	}

	/**
	 * Array of columnName => fieldName
	 * @return array
	 */
	private function getDBColumnNames(OrmProperty $property = null)
	{
		$properties = $property
			? array($property)
			: $this->properties;

		$columns = array();

		foreach ($properties as $property) {

			// FIXME: cache the following mapping

			$propertyPrefix = strtolower(
				preg_replace(
					'/([a-z])([A-Z])/',
					'$1_$2',
					$property->getName()
				)
			);

			foreach (array_keys($property->getType()->getDbColumns()) as $key) {
				$columns[(
					$propertyPrefix
					. (
						(!is_int($key) || $key > 0)
							? '_' . $key
							: ''
					)
				)] = $key;
			}
		}

		return $columns;
	}

	/**
	 * Array of columnName => DBType
	 * @return array
	 */
	function getDbColumns(OrmProperty $property = null)
	{
		$properties = $property
			? array($property)
			: $this->properties;

		$columns = array();

		foreach ($properties as $property) {
			$propertyPrefix = strtolower(
				preg_replace(
					'/([a-z])([A-Z])/',
					'$1_$2',
					$property->getName()
				)
			);

			foreach ($property->getType()->getDbColumns() as $key => $type) {
				$columns[(
					$propertyPrefix
					. (
						(!is_int($key) || $key > 0)
							? '_' . $key
							: ''
					)
				)] = $type;
			}
		}

		return $columns;
	}
}

?>
