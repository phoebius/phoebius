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
class OneToManyContainer extends Container
{
	/**
	 * @var OrmProperty
	 */
	private $referentialProperty;

	/**
	 * @var int
	 */
	private $count;

	function __construct(
			IdentifiableOrmEntity $parent,
			IQueryable $children,
			OrmProperty $referentialProperty,
			$readOnly
		)
	{
		$this->referentialProperty = $referentialProperty;

		parent::__construct($parent, $children, $readOnly);
	}

	function getCount()
	{
		if ($this->isFetched()) {
			return parent::getCount();
		}

		$this->count = $this->getEntityQuery()->getCount();

		return $this->count;
	}

	private function getEntityQuery()
	{
		$query = EntityQuery::create($this->getChildren())
			->where(
				Expression::eq(
					$this->referentialProperty, $this->getParentObject()->getId()
				)
			);

		$this->fillQuery($query);

		return $query;
	}

	function clean()
	{
		$this->count = 0;

		return parent::clean();
	}

	function fetch()
	{
		$list = $this->getEntityQuery()->getList();

		$this->mergeList($list);

		if (!$this->isReadonly()) {
			$this->trackClones();
		}

		return $this;
	}

	/**
	 * TODO drop without fetching the objects
	 */
	function save()
	{
		Assert::isFalse($this->isReadonly(), 'cannot save readonly collections');

		foreach ($this->getChildrenForDeletion() as $object) {
			$object->drop();
		}

		foreach ($this->getList() as $list) {
			$object->save();
		}
	}

	/**
	 * TODO drop without fetching the objects
	 */
	function dropAll()
	{
		Assert::isFalse($this->isReadonly(), 'cannot drop readonly collections');

		$this->fetch();
		$this->setList();
		$this->save();

		$this->clean();

		return $this;
	}
}

?>