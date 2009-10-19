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
class OrmProperty implements IOrmProperty
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
	private $dbFields = array();

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
			array $fields,
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
			sizeof($this->type->getDBFields()) < 1
				? new OrmPropertyVisibility(OrmPropertyVisibility::TRANSPARENT)
				: $visibility;

		$this->setDBFields($fields);
	}

	/**
	 * @return OrmProperty
	 */
	private function setDBFields(array $fields)
	{
		Assert::isTrue(
			sizeof($fields)
			== sizeof($this->type->getDBFields()),
			'wrong DB column count for the specified type'
		);

		$this->dbFields = $fields;

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
	function getDBFields()
	{
		return $this->dbFields;
	}

	function toPhpCall()
	{
		$fields = array();
		foreach ($this->dbFields as $field) {
			$fields[] = '\'' . $field . '\'';
		}

		$ctorArguments = array(
			"'{$this->name}'",
			'array(' . join(', ', $fields).')',
			$this->type->toPhpCodeCall(),
			"new OrmPropertyVisibility(OrmPropertyVisibility::{$this->visibility->getId()})",
			$this->unique
				? 'true'
				: 'false'
		);

		return join('', array(
			'new OrmProperty(',
			join(',', $ctorArguments),
			')'
		));
	}
}

?>