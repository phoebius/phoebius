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
 * @ingroup Orm_Expression
 * @aux
 */
final class EntityProperty
{
	/**
	 * @var string
	 */
	private $owner;

	/**
	 * @var OrmProperty
	 */
	private $property;

	/**
	 * @var OrmPropertyType
	 */
	private $type;

	function __construct($owner, OrmProperty $property)
	{
		$this->owner = $owner;
		$this->property = $property;
		$type = $property->getType();

		Assert::isTrue(
			$type->getColumnCount() == 1,
			'composite property querying is not yet supported (`%s`.`%s` is illegal for EntityQuery)',
			$owner,
			$property->getName()
		);
	}

	/**
	 * @return EntityQuery
	 */
	function getOwner()
	{
		return $this->owner;
	}

	/**
	 * @return OrmProperty
	 */
	function getProperty()
	{
		Return $this->property;
	}

	/**
	 * @return SqlColumn
	 */
	function getSqlColumn()
	{
		$fields = $this->property->getFields();

		$field = reset($fields);

		return new SqlColumn($field, $this->owner);
	}
}

?>