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
class OneToManyPartialWorker extends OneToManyWorker
{
	/**
	 * @return void
	 */
	function syncronizeObjects(array $insert, array $update, array $delete)
	{
		Assert::isEmpty($update);

		$childrenTableName = $this->childrenMap->getPhysicalSchema()->getDBTableName();
		$childrenIdName = $this->childrenMap->getPhysicalSchema()->getIdentifierFieldName();
		$parentIdName = $this->parent->map()->getPhysicalSchema()->getIdentifierFieldName();

		foreach ($insert as $id) {
			//UPDATE only
			$query = new UpdateQuery($childrenTableName);
			$query->addFieldAndValue($parentIdName, $this->parent->getId());
			$query->setCondition(Expression::eq($childrenIdName, $id));
			$this->childrenDao->sendQuery($query);
		}

		if (!empty($delete)) {
			$this->children->dropByIds($delete);
		}

		return $this;
	}

	/**
	 * @return array
	 */
	function getList()
	{
		$rows = $this->childrenDao->getCustomRowsByQuery(
			SelectQuery::create()
				->get($this->childrenMap->getPhysicalSchema()->getIdentifierFieldName())
				->from($this->childrenMap->getPhysicalSchema()->getDBTableName())
				->setCondition($this->generateLogic())
		);

		$ids = array();
		foreach ($rows as $row){
			$ids[] = reset($row);
		}

		return $ids;
	}
}

?>