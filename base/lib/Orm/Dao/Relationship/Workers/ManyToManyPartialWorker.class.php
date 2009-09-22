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
 * @ingroup RelationshipDaoWorkers
 */
class ManyToManyPartialWorker extends ManyToManyWorker
{
	/**
	 * @return void
	 */
	function syncronizeObjects(array $insert, array $update, array $delete)
	{
		Assert::isEmpty($update);

		// insert
		$this->createAssocToChildrenIds($insert);

		//drop
		$this->dropAssocByChildrenIds($delete);
	}

	/**
	 * @return array
	 */
	function getList()
	{
		return $this->getChildrenIds();
	}
}

?>