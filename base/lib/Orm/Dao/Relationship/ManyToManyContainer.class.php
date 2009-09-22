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
	 * @return string
	 */
	abstract function getHelperTableName();

	/**
	 * @return SqlColumn
	 */
	abstract protected function getParentFkColumn();

	/**
	 * @return SqlColumn
	 */
	abstract protected function getChildFkColumn();

	/**
	 * @param OrmEntity $parent
	 * @param OrmMap $children
	 * @param boolean $partialFetch
	 */
	function __construct(OrmEntity $parent, OrmMap $children, $partialFetch = false)
	{
		parent::__construct($parent, $children);

		$worker =
			$partialFetch
				? 'ManyToManyPartialWorker'
				: 'ManyToManyFullWorker';

		$this->setWorker(
			new $worker(
				$this->getHelperTableName(),
				$parent,
				$children,
				$this->getParentFkColumn(),
				$this->getChildFkColumn()
			)
		);
	}
}

?>