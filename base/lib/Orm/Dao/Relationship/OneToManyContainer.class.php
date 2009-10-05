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
abstract class OneToManyContainer extends Container
{
	/**
	 * @return OrmProperty
	 */
	abstract function getReferentialProperty();

	function __construct(OrmEntity $parent, OrmMap $children)
	{
		parent::__construct($parent, $children);

		$this->setWorker(new OneToManyFullWorker($parent, $children, $this->getReferentialProperty()));
	}
}

?>