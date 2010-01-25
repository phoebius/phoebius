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
 * Sets the expression that eliminates group rows that do not satisfy the condition.
 *
 * @ingroup Orm_Query_Projections
 */
final class HavingProjection implements IProjection
{
	/**
	 * @var IExpression
	 */
	private $expression;

	/**
	 * @param IExpression $expression expression to use
	 */
	function __construct(IExpression $expression)
	{
		$this->expression = $expression;
	}

	function fill(SelectQuery $selectQuery, EntityQuery $entityQuery)
	{
		$selectQuery->having(
			$this->expression->toSubjected($entityQuery)
		);
	}
}

?>