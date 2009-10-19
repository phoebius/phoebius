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
class OneToManyFullWorker extends OneToManyWorker
{
	/**
	 * @return void
	 */
	function syncronizeObjects(array $insert, array $update, array $delete)
	{
		if (!empty($delete)) {
			$ids = array();

			foreach ($delete as $id) {
				$ids[] = $id->getId();
			}

			$this->children->getDao()->dropByIds($ids);
		}

		foreach ($insert as $object) {
			$this->children->getDao()->save($object);
		}

		foreach ($update as $object) {
			$this->children->getDao()->save($object);
		}

		return $this;
	}

	/**
	 * @return array
	 */
	function getList()
	{
		return $this->children->getDao()->getListBy($this->getParentsChildrenExpression());
	}
}

?>