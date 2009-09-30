<?php
/* ***********************************************************************************************
 *
 * Phoebius Framework
 *
 * **********************************************************************************************
 *
 * Copyright notice
 *
 ************************************************************************************************/

/**
 * @ingroup RelationshipDao
 */
abstract class Container implements IteratorAggregate
{
	/**
	 * @var ContainerWorker
	 */
	private $worker;

	/**
	 * @var IDalExpression|null
	 */
	private $condition = null;

	/**
	 * @var IdentifiableOrmEntity
	 */
	private $parent;

	/**
	 * @var IQueried
	 */
	private $children;


	/**
	 * @var IOrmEntityAccessor
	 */
	private $childrenDao;

	/**
	 * Use partial fetching - only the IDs of the childen
	 * @var boolean
	 */
	private $partialFetch = true;

	/**
	 * Supresses changes in list to avoid useless memory usage
	 * @var boolean
	 */
	private $readOnly = false;

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
	private $fetchedObjectClones = array();

	/**
	 * Readonly affects save() method only
	 */
	function __construct(
			IdentifiableOrmEntity $parent,
			IQueried $children,
			$readOnly = false,
			$partialFetch = false
		)
	{
		Assert::isBoolean($readOnly);
		Assert::isBoolean($partialFetch);
		Assert::isTrue(
			!!$parent->getId(),
			'cannot track unsaved entities (i.e. with empty ids)'
		);

		$this->parent = $parent;
		$this->children = $children;
		$this->childrenDao = $children->getDao();
		$this->partialFetch = $partialFetch;
	}

	/**
	 * @return ArrayIterator
	 */
	function getIterator()
	{
		return new ArrayIterator($this->getList());
	}

	/**
	 * @return Container an object itself
	 */
	protected function setWorker(ContainerWorker $worker)
	{
		$this->worker = $worker;

		return $this;
	}

	/**
	 * @return ContainerWorker
	 */
	protected function getWorker()
	{
		Assert::isNotNull($this->worker, 'no worker is set');

		return $this->worker;
	}

	/**
	 * @return IQueried
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
	 * @return Container an object itself
	 */
	function setCondition(IDalExpression $condition = null)
	{
		$this->condition = $condition;

		return $this;
	}

	/**
	 * @return IDalExpression|null
	 */
	function getCondition()
	{
		return $this->condition;
	}

	/**
	 * @return boolean
	 */
	function isPartialFetch()
	{
		return $this->partialFetch;
	}

	/**
	 * @return boolean
	 */
	function isReadOnly()
	{
		return $this->readOnly;
	}

	/**
	 * @return integer
	 */
	function getCount()
	{
		if (!$this->isFetched()) {
			return $this->worker->getCount();
		}

		return count($this->list);
	}

	/**
	 * @return boolean
	 */
	function isFetched()
	{
		return $this->isFetched;
	}

	/**
	 * @return Container an object itself
	 */
	function fetch()
	{
		$set = $this->worker->getList();

		$this->mergeList($set);

		if (!$this->readOnly) {
			$this->makeClones();
		}

		$this->isFetched = true;

		return $this;
	}

	/**
	 * @return Container an object itself
	 */
	function save()
	{
		Assert::isFalse(
			$this->readOnly,
			'cannot save a readonly list'
		);

		Assert::isTrue(
			$this->isFetched,
			'cannot save unfetched list'
		);

		$clones	= $this->fetchedObjectClones;

		$ids = $insert = $delete = $update = array();

		if ($this->partialFetch) {
			foreach ($this->list as $id) {
				if (!isset($clones[$id])) {
					$insert[] = $id;
				}
				else {
					$ids[$id] = $id;
				}
			}

			foreach ($clones as $id) {
				if (!isset($ids[$id])) {
					$delete[] = $id;
				}
			}
		}
		else {
			foreach ($this->list as $object) {
				$id = $object->getId();
				if (!$id || !isset($clones[$id])) {
					$insert[] = $object;
				}
				else if (
						   isset($clones[$id])
						&& (($object !== $clones[$id]) || ($object != $clones[$id]))
				) {
					$update[] = $object;
				}

				if ($id) {
					$ids[$id] = $object;
				}
			}

			foreach ($clones as $id => $object) {
				if (!isset($ids[$id])) {
					$delete[] = $object;
				}
			}
		}

		$this->worker->syncronizeObjects($insert, $update, $delete);
		$this->fetchedObjectClones = array();
		$this->makeClones();

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
	function clean()
	{
		$this->list = $this->fetchedObjectClones = array();

		$this->isFetched = false;

		return $this;
	}

	/**
	 * @return integer number of deleted rows
	 */
	function dropList()
	{
		$number = $this->worker->dropList();

		$this->clean();

		return $number;
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

		return $this;
	}

	/**
	 * @return array
	 */
	function getList()
	{
		if (empty($this->list) && !$this->isFetched()) {
			$this->fetch();
		}

		return $this->list;
	}

	/**
	 * @return Container an object itself
	 */
	private function makeClones()
	{
		// bogus check
		if ($this->readOnly) {
			return $this;
		}

		if ($this->partialFetch) {
			foreach ($this->list as $id) {
				$this->fetchedObjectClones[$id] = $id;
			}
		}
		else {
			foreach ($this->list as $object) {
				if (($id = $object->getId())) {
					$this->fetchedObjectClones[$id] = clone $object;
				}
			}
		}

		return $this;
	}
}

?>