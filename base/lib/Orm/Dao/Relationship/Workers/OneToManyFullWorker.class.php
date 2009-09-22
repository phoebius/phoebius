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

			$this->childrenDao->dropByIds($ids);
		}

		foreach ($insert as $object) {
			$this->childrenDao->save($object);
		}

		foreach ($update as $object) {
			$this->childrenDao->save($object);
		}

		return $this;
	}

	/**
	 * @return array
	 */
	function getList()
	{
		return $this->childrenDao->getListByCondition($this->generateLogic());
	}
}

?>