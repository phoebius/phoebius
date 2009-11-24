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

class EntityProjection implements IProjection
{
	/**
	 * @var IQueryable
	 */
	private $entity;

	function __construct(IQueryable $entity)
	{
		$this->entity = $entity;
	}

	function fill(SelectQuery $selectQuery, EntityQuery $entityQuery)
	{
		foreach ($this->entity->getPhysicalSchema()->getDBFields() as $field) {
			$this->injectField($selectQuery, $field);
		}
	}

	protected function injectField(SelectQuery $selectQuery, $field, EntityQuery $entityQuery)
	{
		$selectQuery->get(
			new SqlColumn($field, $entityQuery->getAlias())
		);
	}
}

?>