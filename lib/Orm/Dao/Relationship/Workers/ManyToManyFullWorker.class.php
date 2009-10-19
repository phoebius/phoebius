<?php
/* ***********************************************************************************************
 *
 * Phoebius Framework
 *
 * **********************************************************************************************
 *
 * Copyright (c) 2009 phoebius.org
 *
 * This program is free software; you can redistribute it and/or modify it under the terms
 * of the GNU Lesser General Public License as published by the Free Software Foundation;
 * either version 3 of the License, or (at your option) any later version.
 *
 * You should have received a copy of the GNU Lesser General Public License along with
 * this program; if not, see <http://www.gnu.org/licenses/>.
 *
 ************************************************************************************************/

/**
 * @ingroup Orm_Dao
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