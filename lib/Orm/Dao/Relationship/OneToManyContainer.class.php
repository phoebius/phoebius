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

		$this->count = $this->getSelectQuery()->getCount();

		return $this->count;
	}

	private function getSelectQuery()
	{
		$query = clone $this->getQuery();

		$this->fillQuery($query);

		return $query;
	}

	private function fillQuery(EntityQuery $query)
	{
		$query->andWhere(
			Expression::eq(
				$this->referentialProperty, $this->getParentObject()
			)
		);
	}

	function clean()
	{
		$this->count = 0;

		return parent::clean();
	}

	function fetch()
	{
		$list = $this->getSelectQuery()->getList();

		$this->mergeList($list);

		if (!$this->isReadonly()) {
			$this->trackClones();
		}

		return $this;
	}

	function save()
	{
		Assert::isFalse($this->isReadonly(), 'cannot save readonly collections');

		$getter = $this->referentialProperty->getGetter();
		$setter = $this->referentialProperty->getSetter();

		if ($this->referentialProperty->getMultiplicity()->isNullable()) {
			foreach ($this->getLostTracked() as $object) {
				$object->{$setter}(null);
				$object->save();
			}
		}
		else if (sizeof($this->getLostTracked())) {
			$query = new EntityQuery($this->getChildren());
			$this->fillQuery($query);

			$query->andWhere(
				Expression::in(
					$this->getChildren()->getLogicalSchema()->getIdentifier(),
					$this->getLostTracked()
				)
			);

			$query->delete();
		}

		foreach ($this->getList() as $object) {
			// avoid useless mutation
			if ($object->{$getter}() !== $this->getParentObject()) {
				$object->{$setter}($this->getParentObject());
			}
			$object->save();
		}
	}

	function dropAll()
	{
		Assert::isFalse($this->isReadonly(), 'cannot drop readonly collections');

		$query = new EntityQuery($this->getChildren());
		$this->fillQuery($query);

		$count = $query->delete();

		$this->clean();

		return $count;
	}
}

?>