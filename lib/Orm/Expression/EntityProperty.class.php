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
 */
final class EntityProperty
{
	/**
	 * @var EntityQuery
	 */
	private $entityQuery;

	/**
	 * @var OrmProperty
	 */
	private $property;

	/**
	 * @return EntityExpression
	 */
	static function create(EntityQuery $entityQuery, OrmProperty $property)
	{
		return new self ($entityQuery, $property);
	}

	function __construct(EntityQuery $entityQuery, OrmProperty $property)
	{
		$this->entityQuery = $entityQuery;
		$this->property = $property;
	}

	/**
	 * @return EntityQuery
	 */
	function getEntityQuery()
	{
		return $this->entityQuery;
	}

	/**
	 * @return OrmProperty
	 */
	function getProperty()
	{
		Return $this->property;
	}

	/**
	 * @return array
	 */
	function getSqlColumns()
	{
		$yield = array();

		foreach ($this->property->getDBFields() as $key) {
			$yield[] = new SqlColumn(
				$key,
				$this->entityQuery->getAlias()
			);
		}

		return $yield;
	}
}

?>