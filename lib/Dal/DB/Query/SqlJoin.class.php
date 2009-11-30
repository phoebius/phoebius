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
 * Represents an abstract SelectQuerySource joiner
 *
 * @ingroup Dal_DB_Query
 * @aux
 */
abstract class SqlJoin implements ISqlCastable
{
	/**
	 * @var SelectQuerySource
	 */
	private $source;

	/**
	 * @var SqlJoinMethod
	 */
	private $joinMethod;

	/**
	 * @param SelectQuerySource $source source to which join operation should be applied
	 * @param SqlJoinMethod $joinMethod method to use when performing join
	 */
	function __construct(SelectQuerySource $source, SqlJoinMethod $joinMethod)
	{
		$this->source = $source;
		$this->joinMethod = $joinMethod;
	}

	/**
	 * Gets the source to which join operation is applied
	 * @return SelectQuerySource
	 */
	protected function getSource()
	{
		return $this->source;
	}

	/**
	 * Gets the join method
	 * @return SqlJoinMethod
	 */
	protected function getJoinMethod()
	{
		return $this->joinMethod;
	}
}

?>