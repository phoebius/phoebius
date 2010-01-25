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
 * Represents a queried property requested during the cascaded association resolve process
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
	 * @param EntityQueryBuilder $builder
	 * @param OrmProperty $property
	 */
	function __construct(EntityQueryBuilder $builder, OrmProperty $property)
	{
		$this->owner = $builder->getAlias();
		$this->property = $property;

		Assert::isTrue(
			$property->getType()->getColumnCount() == 1,
			'composite property querying is not supported (`%s`.`%s` is ambiguous)',
			$builder->getEntity()->getLogicalSchema()->getEntityName(),
			$property->getName()
		);
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