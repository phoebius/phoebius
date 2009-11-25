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

class AggrProjection extends PropertyProjection
{
	private $func;

	function __construct($func, $property, $alias = null)
	{
		Assert::isScalar($func);

		$this->func = $func;

		parent::__construct($property, $alias);
	}

	function getFunc(EntityQueryBuilder $entityQueryBuilder)
	{
		return $this->func;
	}

	function fill(SelectQuery $selectQuery, EntityQueryBuilder $entityQueryBuilder)
	{
		$selectQuery->get($this->getSqlFunction($entityQueryBuilder));
	}

	/**
	 * @return SqlFunction
	 * @param EntityQuery $entityQuery
	 */
	protected function getSqlFunction(EntityQueryBuilder $entityQueryBuilder)
	{
		return
			new SqlFunction(
				$this->getFunc($entityQueryBuilder),
				$this->getValueExpression($entityQueryBuilder)
			);
	}
}

?>