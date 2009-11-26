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
	private $isUnique;

	/**
	 * @var OrmPropertyVisibility
	 */
	private $visibility;

	/**
	 * @var array
	 */
	private $fields = array();

	/**
	 * @var boolean
	 */
	private $isIdentifier;

	/**
	 * @param string $name name of the property
	 * @param array list of database field names
	 * @param OrmPropertyType property type
	 * @param false property visibility
	 * @param boolean whether the property unique or not
	 */
	function __construct(
			$name,
			array $fields,
			OrmPropertyType $type,
			OrmPropertyVisibility $visibility,
			$isUnique = false,
			$isIdentifier = false
		)
	{
		Assert::isScalar($name);
		Assert::isBoolean($isUnique);
		Assert::isBoolean($isIdentifier);

		$this->name = $name;

		Assert::isTrue(
			sizeof($fields) == sizeof($type->getSqlTypes()),
			'wrong DB field count'
		);
		$this->fields = $fields;

		$this->type = $type;
		$this->visibility =
			sizeof($this->type->getSqlTypes()) < 1
				? new OrmPropertyVisibility(OrmPropertyVisibility::TRANSPARENT)
				: $visibility;
		$this->isUnique = $isUnique;
		$this->isIdentifier = $isIdentifier;
	}

	/**
	 * @return boolean
	 */
	function isIdentifier()
	{
		return $this->isIdentifier;
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
		return $this->isUnique;
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
	function getFields()
	{
		return $this->fields;
	}

	function toPhpCall()
	{
		$fields = array();
		foreach ($this->fields as $field) {
			$fields[] = '\'' . $field . '\'';
		}

		$ctorArguments = array(
			"'{$this->name}'",
			'array(' . join(', ', $fields).')',
			$this->type->toPhpCodeCall(),
			"new OrmPropertyVisibility(OrmPropertyVisibility::{$this->visibility->getId()})",
			$this->isUnique
				? 'true'
				: 'false',
			$this->isIdentifier
				? 'true'
				: 'false'
		);

		return join('', array(
			'new OrmProperty(',
				join(', ', $ctorArguments),
			')'
		));
	}
}

?>