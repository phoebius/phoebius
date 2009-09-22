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

	function __construct(OrmEntity $parent, OrmMap $children, $partialFetch = false)
	{
		parent::__construct($parent, $children, $partialFetch);

		$worker =
			$partialFetch
				? 'OneToManyPartialWorker'
				: 'OneToManyFullWorker';

		$this->setWorker(new $worker($parent, $children, $this->getReferentialProperty()));
	}
}

?>