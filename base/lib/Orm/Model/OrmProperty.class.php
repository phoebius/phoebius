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
class OrmProperty
{
	/**
	 * @var string
	 */
	private $name;

	/**
	 * @var OrmPropertyType
	 */
	private $type;

	/**
	 * @var boolean
	 */
	private $unique;

	/**
	 * @var OrmPropertyVisibility
	 */
	private $visibility;

	/**
	 * @var array
	 */
	private $dbColumnNames = array();

	/**
	 * @return OrmProperty
	 */
	static function create(
			$name,
			OrmPropertyType $type,
			OrmPropertyVisibility $visibility,
			$isUnique = false
		)
	{
		return new self ($name, $type, $visibility, $isUnique);
	}

	/**
	 * @param scalar $name property name
	 * @param IPropertyMappable $type type of a property
	 * @param boolean $isUnique
	 */
	function __construct(
			$name,
			OrmPropertyType $type,
			OrmPropertyVisibility $visibility,
			$isUnique = false
		)
	{
		Assert::isScalar($name);
		Assert::isBoolean($isUnique);

		$this->name = $name;
		$this->type = $type;
		$this->unique = $isUnique;
		$this->visibility =
			sizeof($this->type->getDbColumns()) < 1
				? new OrmPropertyVisibility(OrmPropertyVisibility::TRANSPARENT)
				: $visibility;

		$this->autoFillColumnNames();
	}

	/**
	 * @return OrmProperty
	 */
	function setDBColumnNames(array $dbColumnNames)
	{
		Assert::isTrue(
			sizeof($dbColumnNames)
			== sizeof($this->type->getDbColumns()),
			'wrong DB column count for the specified type'
		);

		$this->dbColumnNames = $dbColumnNames;

		return $this;
	}

	/**
	 * @return string
	 */
	function getGetter()
	{
		if (!$this->visibility->isGettable()) {
			throw new OrmModelPropertyException($this, 'cannot have getter');
		}

		return 'get' . ucfirst($this->name);
	}

	/**
	 * @return string
	 */
	function getSetter()
	{
		if (!$this->visibility->isSettable()) {
			throw new OrmModelPropertyException($this, 'cannot have setter');
		}

		return 'set' . ucfirst($this->name);
	}

	/**
	 * @return string
	 */
	function getName()
	{
		return $this->name;
	}

	/**
	 * @return boolean
	 */
	function isUnique()
	{
		return $this->unique;
	}

	/**
	 * @return OrmPropertyType
	 */
	function getType()
	{
		return $this->type;
	}

	/**
	 * @return OrmPropertyVisibility
	 */
	function getVisibility()
	{
		return $this->visibility;
	}

	/**
	 * @return array of columnName
	 */
	function getDbColumns()
	{
		return $this->dbColumnNames;
	}

	private function autoFillColumnNames()
	{
		$columns = array();

		$propertyPrefix = strtolower(
			preg_replace(
				'/([a-z])([A-Z])/',
				'$1_$2',
				$this->getName()
			)
		);

		foreach (array_keys($this->getType()->getDbColumns()) as $key) {
			$columns[] = (
				$propertyPrefix
				. (
					(!is_int($key) || $key > 0)
						? '_' . $key
						: ''
				)
			);
		}

		$this->setDBColumnNames($columns);
	}
}

?>