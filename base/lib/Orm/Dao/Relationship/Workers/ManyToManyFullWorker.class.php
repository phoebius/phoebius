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
class ManyToManyFullWorker extends ManyToManyWorker
{
	/**
	 * @return void
	 */
	function syncronizeObjects(array $insert, array $update, array $delete)
	{
		// insert
		$this->createAssocToChildrenIds($insert);

		//update object, not relation
		foreach ($update as $object) {
			$this->children->getDao()->save($object);
		}

		//drop
		$this->dropAssocByChildrenIds($delete);
	}

	/**
	 * @return array of OrmEntity
	 */
	function getList()
	{
		return $this->children->getDao()->getByIds(
			$this->getChildrenIds()
		);
	}
}

?>