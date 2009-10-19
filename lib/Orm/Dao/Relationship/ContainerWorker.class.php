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
abstract class ContainerWorker
{
	/**
	 * @var IdentifiableOrmEntity
	 */
	protected $parent;

	/**
	 * @var IQueryable
	 */
	protected $children;

	/**
	 * @var EntityQuery|null
	 */
	private $entityQuery = null;

	/**
	 * @return void
	 */
	abstract function syncronizeObjects(array $insert, array $update, array $delete);

	/**
	 * @return array
	 */
	abstract function getList();

	/**
	 * @return integer
	 */
	abstract function dropList();

	/**
	 * @return integer
	 */
	abstract function getCount();

	function __construct(IdentifiableOrmEntity $parent, IQueryable $children)
	{
		$this->parent = $parent;
		$this->children = $children;
	}

	/**
	 * @return ContainerWorker an object itself
	 */
	final function setEntityQuery(EntityQuery $entityQuery = null)
	{
		Assert::isTrue(
			$entityQuery->getEntity()->getLogicalSchema()->getEntityName()
			== $this->children->getLogicalSchema()->getEntityName(),
			'queries against %s are allowed only',
			$this->children->getLogicalSchema()->getEntityName()
		);

		$this->entityQuery = $entityQuery;

		return $this;
	}

	/**
	 * @return EntityQuery|null
	 */
	function getEntityQuery()
	{
		return $this->entityQuery;
	}
}

?>