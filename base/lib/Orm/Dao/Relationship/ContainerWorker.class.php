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
abstract class ContainerWorker
{
	/**
	 * @var IdentifiableOrmEntity
	 */
	protected $parent;

	/**
	 * @var EntityQuery|null
	 */
	private $entityQuery = null;

	/**
	 * @var OrmClass
	 */
	protected $children;

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

	function __construct(IdentifiableOrmEntity $parent, IQueried $children)
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