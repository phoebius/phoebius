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

		$childrenTableName = $this->children->getPhysicalSchema()->getDBTableName();
		$childrenIdName = $this->children->getLogicalSchema()->getIdentifier()->getName();
		$parentIdName = $this->parent->map()->getPhysicalSchema()->getIdentifierFieldName();

		foreach ($insert as $id) {
			//UPDATE only
			$query = new UpdateQuery($childrenTableName);
			$query->addFieldAndValue($parentIdName, $this->parent->getId());
			$query->setCondition(Expression::eq($childrenIdName, $id));
			$this->childrenDao->sendQuery($query);
		}

		if (!empty($delete)) {
			$this->children->getDao()->dropByIds($delete);
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
				->getFields($this->children->getPhysicalSchema()->getDbColumns($this->referentialProperty))
				->from($this->children->getPhysicalSchema()->getDBTableName())
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