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
abstract class Container implements IteratorAggregate
{
	/**
	 * @var IdentifiableOrmEntity
	 */
	private $parent;

	/**
	 * @var IQueryable
	 */
	private $children;

	/**
	 * @var EntityQuery|null
	 */
	private $entityQuery;

	/**
	 * @var boolean
	 */
	private $isFetched = false;

	/**
	 * @var array
	 */
	private $list = array();

	/**
	 * @var array
	 */
	private $tracked = array();

	/**
	 * @var boolean
	 */
	private $readOnly;

	/**
	 * @var EntityQuery
	 */
	private $query;

	/**
	 * @return Container an object itself
	 */
	abstract function fetch();

	/**
	 * @return Container an object itself
	 */
	abstract function save();

	/**
	 * Drops the entire set of associations
	 * @return integer number of deleted rows
	 */
	abstract function dropAll();

	function __construct(
			IdentifiableOrmEntity $parent,
			IQueryable $children,
			$readOnly = true
		)
	{
		Assert::isBoolean($readOnly);

		Assert::isTrue(
			!!$parent->getId(),
			'cannot track children of unsaved parent'
		);

		$this->parent = $parent;
		$this->children = $children;
		$this->readOnly = $readOnly;
		$this->query = new EntityQuery($this->children);
	}

	/**
	 * @return ArrayIterator
	 */
	function getIterator()
	{
		return new ArrayIterator($this->getList());
	}

	/**
	 * @return IQueryable
	 */
	protected function getChildren()
	{
		return $this->children;
	}

	/**
	 * @return IdentifiableOrmEntity
	 */
	function getParentObject()
	{
		return $this->parent;
	}

	/**
	 * Must be overridden to optimize calls
	 * @return integer
	 */
	function getCount()
	{
		return sizeof($this->getList());
	}

	/**
	 * @return boolean
	 */
	function isFetched()
	{
		return $this->isFetched;
	}

	/**
	 * @return boolean
	 */
	function isReadonly()
	{
		return $this->readOnly;
	}

	/**
	 * @return array
	 */
	function getList()
	{
		if (!$this->isFetched) {
			$this->fetch();
		}

		return $this->list;
	}

	/**
	 * Just cleans fetched list
	 * @return Container an object itself
	 */
	function clean()
	{
		$this->list = array();
		$this->tracked = array();
		$this->isFetched = false;

		return $this;
	}

	/**
	 * @return Container an object itself
	 */
	function setList(array $list)
	{
		$this->list = $list;

		$this->isFetched = true;

		return $this;
	}

	/**
	 * @return Container an object itself
	 */
	function mergeList(array $list)
	{
		foreach ($list as $item) {
			$this->list[] = $item;
		}

		$this->isFetched = true;

		$this->list = array_unique($this->list, SORT_REGULAR);

		return $this;
	}

	function setQuery(EntityQuery $query)
	{
		Assert::isTrue($query->getQueryRoot() === $this->children);

		$this->query = $query;

		return $this;
	}

	/**
	 * @return EntityQuery
	 */
	protected function getQuery()
	{
		return $this->query;
	}

	/**
	 * @return Container
	 */
	protected function trackClones()
	{
		foreach ($this->list as $object) {
			$this->tracked[] = spl_object_hash($object);
		}

		return $this;
	}

	/**
	 * Get the list of objects added after actual fetch
	 * @return array
	 */
	protected function getUntracked()
	{
		$yield = array();

		foreach ($this->list as $object) {
			$hash = spl_object_hash($object);
			if (!in_array($hash, $this->tracked)) {
				$yield[] = $object;
			}
		}

		return $yield;
	}

	/**
	 * Track objects with broken refs
	 *
	 * @return array
	 */
	protected function getLostTracked()
	{
		$yield = array();
		foreach ($this->tracked as $hash) {
			foreach ($this->list as $object) {
				if ($hash == spl_object_hash($object)) { // preserved
					continue (2);
				}
			}

			$yield[] = $object;
		}

		return $yield;
	}
}

?>