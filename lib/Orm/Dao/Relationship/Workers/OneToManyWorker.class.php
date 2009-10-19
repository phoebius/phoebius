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
 * @ingroup Orm_Dao
 */
abstract class OneToManyWorker extends ContainerWorker
{
	/**
	 * @var OrmProperty
	 */
	protected $referentialProperty;

	final function __construct(
			OrmEntity $parent,
			OrmClass $children,
			OrmProperty $referentialProperty
		)
	{
		$this->referentialProperty = $referentialProperty;

		parent::__construct($parent, $children);
	}

	/**
	 * @return integer
	 */
	function dropList()
	{
		return $this->children->getDao()->dropBy($this->getParentsChildrenExpression());
	}

	/**
	 * @return integer
	 */
	function getCount()
	{
		$row = $this->children->getDao()->getCustomRowByQuery(
			SelectQuery::create()
				->getExpression(
					SqlFunction::create('count')->aggregateWithNulls()
				)
				->from(
					$this->children->getPhysicalSchema()->getDBTableName()
				)
				->setExpression(
					$this->getParentsChildrenExpression()
				)
		);

		Assert::isNotEmpty($row);

		$count = reset($row);

		Assert::isNumeric($count);

		return $count;
	}

	/**
	 * @return IDalExpression
	 */
	protected function getParentsChildrenExpression()
	{
		return
			EntityQuery::create($this->children)
				->where(
					$this->referentialProperty,
					Expression::eq(
						$this->parent
					)
				);
	}
}

?>