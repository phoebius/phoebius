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
abstract class ManyToManyContainer extends Container
{
	/**
	 * @var ManyToManyContainerPropertyType
	 */
	private $proxy;

	/**
	 * @param OrmEntity $parent
	 * @param OrmMap $children
	 * @param boolean $partialFetch
	 */
	function __construct(OrmEntity $parent, OrmMap $children, ManyToManyContainerPropertyType $mtmType, $partialFetch = false)
	{
		parent::__construct($parent, $children);

		$worker =
			$partialFetch
				? 'ManyToManyPartialWorker'
				: 'ManyToManyFullWorker';

		$this->setWorker(
			new $worker(
				$parent,
				$children,
				$mtmType
			)
		);
	}
}

?>