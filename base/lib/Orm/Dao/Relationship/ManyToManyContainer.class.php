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
	private $mtm;

	/**
	 * @param OrmEntity $parent
	 * @param OrmMap $children
	 * @param boolean $partialFetch
	 */
	function __construct(
			IdentifiableOrmEntity $parent,
			IQueried $children,
			ManyToManyContainerPropertyType $proxy
		)
	{
		parent::__construct($parent, $children);
		$this->setWorker(
			new ManyToManyFullWorker(
				$parent,
				$children,
				$proxy
			)
		);
	}
}

?>