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
 * Represents a chain of projections
 *
 * @ingroup Orm_Query_Projections
 */
final class ProjectionChain extends TypedValueArray implements IProjection
{
	/**
	 * @param array $array initial IProjection objects to be append to the value list
	 */
	function __construct(array $array = array())
	{
		parent::__construct('IProjection', $array);
	}

	function fill(SelectQuery $selectQuery, EntityQueryBuilder $entityQueryBuilder)
	{
		foreach ($this->toArray() as $projection) {
			$projection->fill($selectQuery, $entityQueryBuilder);
		}
	}

	function __clone()
	{
		$elements = $this->toArray();

		$this->erase();

		foreach ($elements as $element) {
			$this->append(clone $element);
		}
	}
}

?>