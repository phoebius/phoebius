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
abstract class ManyToManyWorker extends ContainerWorker
{
	/**
	 * @var ManyToManyContainerPropertyType
	 */
	protected $mtm;

	final function __construct(
			IdentifiableOrmEntity $parent,
			IQueryable $children,
			ManyToManyContainerPropertyType $mtm
		)
	{
		$this->mtm = $mtm;

		parent::__construct($parent, $children);
	}

	/**
	 * @return integer
	 */
	function dropList()
	{
		$count = $this->mtm->getProxy()->getDao()->dropBy(
			EntityQuery::create($this->mtm->getProxy())
				->where(
					$this->mtm->getContainerProxyProperty(),
					Expression::eq($this->parent)
				)
		);

		return (int)$count;
	}

	/**
	 * @return array
	 */
	protected function getChildrenIds()
	{
		$entityQuery = EntityQuery::create($this->mtm->getProxy())
			->where(
				// maps proxy.parent_id<->parent.id as value
				$this->mtm->getContainerProxyProperty(),
				Expression::eq(
					$this->parent
				)
			)
			->andWhere(
				// maps proxy.child_id<->child.id
				$this->mtm->getEncapsulantProxyProperty(),
				Expression::eq(
					EntityQuery::create($this->children)
						->getPropertyClause($this->children->getIdentifier())
				)
			);

		if (($limitedEntityQuery = $this->getEntityQuery())) {
			$entityQuery->merge(
				$this->mtm->getEncapsulantProxyProperty(),
				$limitedEntityQuery
			);
		}

		$childGetter = $this->mtm->getEncapsulantProxyProperty()->getGetter();

		$ids = array();

		foreach ($entityQuery->getList() as $object) {
			$ids[] = $object->{$childGetter}();
		}

		return $ids;
	}

	/**
	 * Create *:* associations where the specified children are assigned. Both the list of IDs and the
	 * list of children object (instanceof ChildObject) are supported
	 * @return int number of deleted associations
	 */
	protected function createAssocToChildrenIds(array $children)
	{
		$proxyDao = $this->mtm->getProxy()->getDao();

		$proxyObjProto = $this->mtm->getProxy()->getLogicalSchema()->getNewEntity();
		$proxyObjProto->{$this->mtm->getContainerProxyProperty()->getSetter()}($this->parent);

		$childSetter = $this->mtm->getEncapsulantProxyProperty()->getSetter();

		foreach ($children as $child) {
			$proxyObj = clone $proxyObjProto;
			$proxyObj->{$childSetter}($child);

			$proxyDao->save($proxyObj);
		}
	}

	/**
	 * Drop *:* associations where the specified children are assigned. Both the list of IDs and the
	 * list of children object (instanceof ChildObject) are supported
	 * @return int number of deleted associations
	 */
	protected function dropAssocByChildrenIds(array $children)
	{
		if (!empty($children)) {
			$this->mtm->getProxy()->getDao()->dropBy(
				EntityQuery::create($this->mtm->getProxy())
					->where(
						$this->mtm->getContainerProxyProperty(),
						Expression::eq($this->parent)
					)
					->where(
						$this->mtm->getEncapsulantProxyProperty(),
						Expression::in(
							$children
						)
					)
			);
		}
	}
}

?>