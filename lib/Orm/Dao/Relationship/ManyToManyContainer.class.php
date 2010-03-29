<?php
/* ***********************************************************************************************
 *
 * Phoebius Framework
 *
 * **********************************************************************************************
 *
 * Copyright (c) 2010 phoebius.org
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
class ManyToManyContainer extends Container
{
	/**
	 * @var ManyToManyContainerPropertyType
	 */
	private $mtm;

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
		$this->mtm = $referentialProperty->getType();

		Assert::isTrue(
			$this->mtm instanceof ManyToManyContainerPropertyType
		);

		parent::__construct($parent, $children, $readOnly);
	}

	function getCount()
	{
		if ($this->isFetched()) {
			return parent::getCount();
		}

		$query = clone $this->getQuery();
		$alias = get_class($this) . '_counter';
		$query->get(
			Projection::count(
				$this->getChildren()->getLogicalSchema()->getIdentifier(),
				$alias
			)
		);

		$sqlQuery = $this->fillQuery($query);

		$result = $this->getChildren()->getDao()->getRow($sqlQuery);
		$this->count = (int) $result[$alias];

		return $this->count;
	}

	private function getSelectQuery()
	{
		$query = clone $this->getQuery();

		$this->fillQuery($query);

		return $query;
	}

	/**
	 * @return ISqlSelectQuery
	 */
	private function fillQuery(EntityQuery $query)
	{
		$query = $query->toSelectQuery();

		$joinMethod = SqlJoinMethod::INNER;
		$childrenTable = $this->getChildren()->getPhysicalSchema()->getTable();
		$proxyTable = $this->mtm->getProxy()->getPhysicalSchema()->getTable();

		$condition = Expression::andChain();
		$srcSqlFields = $this->getChildren()->getLogicalSchema()->getIdentifier()->getFields();

		$dstSqlFields = $this->mtm->getEncapsulantProxyProperty()->getFields();

		foreach ($srcSqlFields as $k => $v) {
			$condition->add(
				Expression::eq(
					new SqlColumn($srcSqlFields[$k], $childrenTable),
					new SqlColumn($dstSqlFields[$k], $proxyTable)
				)
			);
		}

		$query->join(
			new SqlConditionalJoin(
				new SelectQuerySource(
					new SqlIdentifier($proxyTable)
				),
				new SqlJoinMethod($joinMethod),
				$condition
			)
		);

		$columnName = $this->mtm->getContainerProxyProperty()->getField();
		$query->andWhere(
			Expression::eq(
				new SqlColumn(
					$columnName,
					$this->mtm->getProxy()->getPhysicalSchema()->getTable()
				),
				new SqlValue($this->getParentObject()->_getId())
			)
		);

		return $query;
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

		// delete relations
		if (sizeof($this->getLostTracked())) {
			EntityQuery::create($this->mtm->getProxy())
				->where(
					Expression::in(
						$this->mtm->getEncapsulantProxyProperty(),
						$this->getLostTracked()
					)
				)
				->delete();
		}

		// create new relations
		$containerSetter = $this->mtm->getContainerProxyProperty()->getSetter();
		$encapsulantSetter = $this->mtm->getEncapsulantProxyProperty()->getSetter();
		foreach ($this->getUntracked() as $object) {
			$proxy = $this->mtm->getProxy()->getLogicalSchema()->getNewEntity();
			$proxy->{$containerSetter}($this->getParentObject());
			$proxy->{$encapsulantSetter}($object);
			try {
				$proxy->save();
			}
			catch (UniqueViolationException $e) {}
		}
	}

	function dropAll()
	{
		Assert::isFalse($this->isReadonly(), 'cannot drop readonly collections');

		$query = EntityQuery::create($this->mtm->getProxy())
			->where(
				Expression::eq(
					$this->mtm->getContainerProxyProperty(),
					$this->getParentObject()
				)
			);
		$count = $query->delete();

		$this->clean();

		return $count;
	}
}

?>