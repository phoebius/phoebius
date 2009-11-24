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

final class Projection extends StaticClass
{
	/**
	 * @return EntityProjection
	 */
	static function entity($entity)
	{
		return new EntityProjection(
			self::getQueriable($entity)
		);
	}

	/**
	 * @return PropertyProjection
	 */
	static function property($property, $alias = null)
	{
		return new PropertyProjection($property, $alias);
	}

	/**
	 * @return IProjection
	 */
	static function groupBy($group)
	{
		$group = self::getQueriable($group);

		return
			$group instanceof IQueryable
				? new GroupByEntityProjection($group)
				: new GroupByPropertyProjection($group);
	}

	/**
	 * @return HavingProjection
	 */
	static function having(IExpression $expression)
	{
		return new HavingProjection($expression);
	}

	/**
	 * @return RowCountProjection
	 */
	static function count($property, $alias = null)
	{
		return new RowCountProjection($property, $alias);
	}

	/**
	 * @return RowCountProjection
	 */
	static function rowCount($alias = null)
	{
		return new RowCountProjection(null, $alias);
	}

	/**
	 * @return DistinctCountProjection
	 */
	static function distinctCount($property, $alias = null)
	{
		return new DistinctCountProjection($property, $alias);
	}

	/**
	 * @return IQueryable
	 */
	private static function getQueriable($class)
	{
		if (is_object($class) && !$class instanceof IOrmRelated) {
			$class = get_class($class);
		}

		if (is_scalar($class) && TypeUtils::isChild($class, 'IOrmRelated')) {
			$class = call_user_func(array($class, 'orm'));
		}

		return $class;
	}
}

?>