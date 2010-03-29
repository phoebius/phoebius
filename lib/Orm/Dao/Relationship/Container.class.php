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
 * An encapsulant container implementation.
 *
 * A container provides native API to handle collections of objects that refer to an entity.
 *
 * See OneToManyContainer for one-to-many relations, and ManyToManyContainer for many-to-many
 * relations.
 *
 * See Container::setQuery() to filter the results.
 *
 * Container::fetch() fetches the list.
 *
 * Container::getList() provides a list of fetched encapsulants.
 *
 * Container::setList() and Container::mergeList() replaces and merges the list of new encapsulants
 * (missing objects will cause association to be dropped, new objects will create a new association).
 *
 * Finally, Container::save() to save the collection.
 *
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
	 * Fetches the collection
	 *
	 * @return Container an object itself
	 */
	abstract function fetch();

	/**
	 * Saves the collection
	 *
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

	function getIterator()
	{
		return new ArrayIterator($this->getList());
	}

	/**
	 * Gets the children entity info object
	 *
	 * @return IQueryable
	 */
	protected function getChildren()
	{
		return $this->children;
	}

	/**
	 * Gets the parent object children belong to
	 *
	 * @return IdentifiableOrmEntity
	 */
	function getParentObject()
	{
		return $this->parent;
	}

	/**
	 * Gets the number of children belonging to the parent. Must be overridden to optimize calls
	 * @return integer
	 */
	function getCount()
	{
		return sizeof($this->getList());
	}

	/**
	 * Determines whether collection is fetched or not
	 *
	 * @return boolean
	 */
	function isFetched()
	{
		return $this->isFetched;
	}

	/**
	 * Specifies whether collection is readonly or not
	 *
	 * @return boolean
	 */
	function isReadonly()
	{
		return $this->readOnly;
	}

	/**
	 * Gets the collection containing objects
	 *
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
	 * Just cleans a fetched list
	 *
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
	 * Sets the new collection of objects that should belong to the parent object
	 *
	 * @return Container an object itself
	 */
	function setList(array $list)
	{
		$this->list = $list;

		$this->isFetched = true;

		return $this;
	}

	/**
	 * Sets the collection of objects that should belong to the parent object appending it
	 * to already defined collections
	 *
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

	/**
	 * Sets the "filter" object
	 *
	 * @return Container an object itself
	 */
	function setQuery(EntityQuery $query)
	{
		Assert::isTrue($query->getQueryRoot() === $this->children);

		$this->query = $query;

		return $this;
	}

	/**
	 * Gets the query for selecting children
	 *
	 * @return EntityQuery
	 */
	protected function getQuery()
	{
		return $this->query;
	}

	/**
	 * Marks the children that currently defined in a collection as entities with persisten
	 * association to the parent
	 *
	 * @return void
	 */
	protected function trackClones()
	{
		foreach ($this->list as $object) {
			$this->tracked[] = spl_object_hash($object);
		}
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