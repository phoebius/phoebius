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

final class ProjectionChain extends TypedValueArray implements IProjection
{
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