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
		// FIXME: make single JOIN query instead of two
		// to do this we need to expand EntityQuery with projections and
		// expliciting setting of entity Type to fetch
		return $this->children->getDao()->getByIds(
			$this->getChildrenIds()
		);
	}

	/**
	 * @return int
	 * @see Dao/Relationship/ContainerWorker#getCount()
	 */
	function getCount()
	{
		return sizeof($this->getChildrenIds());
	}
}

?>