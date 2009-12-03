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
 * Projection invokes the count aggregate for all distinct non-null values of the specified
 * property or expression found in the input rows
 *
 * @ingroup Orm_Query_Projections
 */
final class DistinctCountProjection extends CountProjection
{
	protected function getSqlFunction(EntityQueryBuilder $entityQueryBuilder)
	{
		return
			SqlFunction::aggregateDistinct(
				$this->getFunc($entityQueryBuilder),
				$this->getValueExpression($entityQueryBuilder)
			);
	}
}

?>