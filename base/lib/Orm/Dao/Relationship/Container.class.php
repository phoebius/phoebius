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
	 * @var IdentifiableOrmEntity
	 */
	private $parent;

	/**
	 * @var IQueried
	 */
	private $children;

	/**
	 * @var EntityQuery|null
	 */
	private $entityQuery = null;

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
	 * Readonly affects save() method only
	 */
	function __construct(
			IdentifiableOrmEntity $parent,
			IQueried $children,
			$readOnly = false
		)
	{
		Assert::isBoolean($readOnly);
		Assert::isTrue(
			!!$parent->getId(),
			'cannot track unsaved entities (i.e. with empty ids)'
		);

		$this->parent = $parent;
		$this->children = $children;
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
	final function setEntityQuery(EntityQuery $entityQuery = null)
	{
		$this->getWorker()->setEntityQuery($entityQuery);

		return $this;
	}

	/**
	 * @return EntityQuery|null
	 */
	function getEntityQuery()
	{
		return $this->getWorker()->getEntityQuery();
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
			return $this->getWorker()->getCount();
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
		$list = $this->getWorker()->getList();

		$this->mergeList($list);

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

		foreach ($this->list as $object) {
			$id = $object->_getId();
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
	 * Just cleans fetched list
	 * @return Container an object itself
	 */
	function clean()
	{
		$this->list = array();
		$this->isFetched = false;

		return $this;
	}

	/**
	 * Drops the entire set of associations
	 * @return integer number of deleted rows
	 */
	function dropList()
	{
		$number = $this->getWorker()->dropList();

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

		$this->list = array_unique($this->list);

		return $this;
	}

	/**
	 * @return array
	 */
	function getList()
	{
		if (empty($this->list) && !$this->isFetched) {
			$this->fetch();
		}

		return $this->list;
	}
}

?>