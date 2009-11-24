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

class RawProjection implements IProjection
{
	private $expression;
	private $alias;

	function __construct($expression, $alias = null)
	{
		$this->expression = $expression;
		$this->alias = $alias;
	}

	function getExpression()
	{
		return $this->expression;
	}

	function getAlias()
	{
		return $this->alias;
	}

	function fill(SelectQuery $selectQuery, EntityQueryBuilder $builder)
	{
		$selectQuery->get($this->getValueExpression($builder));
	}

	/**
	 * @return ISqlValueExpression
	 */
	protected function getValueExpression(EntityQueryBuilder $builder)
	{
		$builder->addId($this->alias);

		return
			new AliasedSqlValueExpression(
				$builder->subject($this->getExpression()),
				$this->alias
			);
	}
}

?>