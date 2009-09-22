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
			$object->dao()->save($object);
		}

		//drop
		$this->dropAssocByChildrenIds($delete);
	}

	/**
	 * @return array
	 */
	function getList()
	{
		return $this->childrenDao->getByIds(
			$this->getChildrenIds()
		);
	}
}

?>