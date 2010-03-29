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
 * Represents an property of ORM-related entity
 *
 * @ingroup Orm_Model
 */
final class OrmProperty
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
	 * @var AssociationMultiplicity
	 */
	private $multiplicity;

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
			AssociationMultiplicity $multiplicity,
			$isUnique = false,
			$isIdentifier = false
		)
	{
		Assert::isScalar($name);
		Assert::isBoolean($isUnique);
		Assert::isBoolean($isIdentifier);

		$this->name = $name;

		Assert::isTrue(
			sizeof($fields) == $type->getColumnCount(),
			'wrong DB field count'
		);
		$this->fields = $fields;

		$this->type = $type;
		$this->visibility = $visibility;
		$this->multiplicity = $multiplicity;
		$this->isUnique = $isUnique;
		$this->isIdentifier = $isIdentifier;
	}

	/**
	 * Gets the property multiplicity
	 * @return AssociationMultiplicity
	 */
	function getMultiplicity()
	{
		return $this->multiplicity;
	}

	/**
	 * Determines whether the property is identifier
	 * @return boolean
	 */
	function isIdentifier()
	{
		return $this->isIdentifier;
	}

	/**
	 * Gets the name of property getter
	 * @return string
	 */
	function getGetter()
	{
		if (!$this->visibility->isGettable()) {
			throw new OrmModelIntegrityException($this->name . 'cannot have getter');
		}

		return 'get' . ucfirst($this->name);
	}

	/**
	 * Gets the name of property setter
	 * @return string
	 */
	function getSetter()
	{
		if (!$this->visibility->isSettable()) {
			throw new OrmModelIntegrityException($this->name . 'cannot have setter');
		}

		return 'set' . ucfirst($this->name);
	}

	/**
	 * Gets the name of the property
	 * @return string
	 */
	function getName()
	{
		return $this->name;
	}

	/**
	 * Determines whether property is used to ensure that is's value is unique with respect to all
	 * the entities stored in the table
	 * @return boolean
	 */
	function isUnique()
	{
		return $this->isUnique;
	}

	/**
	 * Gets the ORM type of the property
	 * @return OrmPropertyType
	 */
	function getType()
	{
		return $this->type;
	}

	/**
	 * Gets the property visibility
	 * @return OrmPropertyVisibility
	 */
	function getVisibility()
	{
		return $this->visibility;
	}

	/**
	 * Gets the names of the fields used to store the property value inside the database
	 * @return array of string
	 */
	function getFields()
	{
		return $this->fields;
	}

	function getField()
	{
		Assert::isTrue(
			sizeof($this->fields) == 1,
			'%s has %s fields',
			$this->name,
			sizeof($this->fields)
		);

		return reset ($this->fields);
	}

	/**
	 * @return string
	 */
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
			"new AssociationMultiplicity(AssociationMultiplicity::{$this->multiplicity->getId()})",
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