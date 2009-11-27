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

class PropertyProjection implements IProjection
{
	private $property;

	function __construct($property)
	{
		$this->property = $property;
	}

	function fill(SelectQuery $selectQuery, EntityQueryBuilder $entityQueryBuilder)
	{
		$property = $entityQueryBuilder->getEntity()->getLogicalSchema()->getProperty($this->property);

		foreach ($property->getFields() as $field) {
			$this->fillPropertyField($field, $selectQuery, $entityQueryBuilder);
		}

		$selectQuery->get($this->getValueExpression($entityQueryBuilder));
	}

	protected function fillPropertyField($field, SelectQuery $selectQuery, EntityQueryBuilder $entityQueryBuilder)
	{
		$selectQuery->get(
			new SqlColumn($field, $entityQueryBuilder->getAlias())
		);
	}
}

?>