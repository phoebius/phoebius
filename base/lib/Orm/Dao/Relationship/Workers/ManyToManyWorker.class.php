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
abstract class ManyToManyWorker extends ContainerWorker
{
	/**
	 * @var ManyToManyContainerPropertyType
	 */
	private $mtm;

	final function __construct(
			IdentifiableOrmEntity $parent,
			IQueried $children,
			ManyToManyContainerPropertyType $mtm
		)
	{
		$this->mtm = $mtm;

		parent::__construct($parent, $children);
	}

	/**
	 * @return string
	 */
	protected function getHelperTableName()
	{
		return $this->mtm->getProxy()->getDBTableName();
	}

	/**
	 * @return integer
	 */
	function dropList()
	{
		$deleteQuery = new DeleteQuery($this->getHelperTableName());

		$deleteQuery->setExpression(
			EntityQuery::create($this->mtm->getProxy())
				->where(
					$this->mtm->getContainerProxyProperty(),
					Expression::eq(
						$this->parent
					)
				)
		);

		$count = $this->children->getDao()->sendQuery($deleteQuery);

		return (int)$count;
	}

	/**
	 * @return IDalExpression
	 */
	private function getJoinLogic()
	{
		return DalExpression::andChain()
			->add(
				EntityQuery::create($this->mtm->getProxy())
					->where(
						$this->mtm->getContainerProxyProperty(),
						Expression::eq(
							$this->parent
						)
					)
			)
			->add(
				EntityQuery::create($this->mtm->getProxy())
					->where(
						$this->mtm->getEncapsulantProxyProperty(),
						Expression::eq(
							$this->children->getIdentifier()
						)
					)
			)
			->add(
				$this->getExpression()
			);
	}

	/**
	 * @return array
	 */
	protected function getChildrenIds()
	{
		$childrenTableName = $this->childrenMap->getPhysicalSchema()->getDBTableName();

		$selectQuery = new SelectQuery();
		$selectQuery->from($this->helperTableName);
		$selectQuery->from($childrenTableName);
		$selectQuery->get(
			$this->childrenMap->getPhysicalSchema()->getIdentifierFieldName(),
			null,
			$childrenTableName
		);
		$selectQuery->setExpression($this->getJoinLogic());

		$rows = $this->childrenDao->getCustomRowsByQuery(
			$selectQuery
		);

		$ids = array();
		foreach ($rows as $row) {
			$ids[] = reset($row);
		}

		return $ids;
	}

	/**
	 * Create *:* associations where the specified children are assigned. Both the list of IDs and the
	 * list of children object (instanceof ChildObject) are supported
	 * @return int number of deleted associations
	 */
	protected function createAssocToChildrenIds(array $children)
	{
		$childClass = $this->childrenMap->getLogicalSchema()->getEntityClassName();

		// insert
		foreach ($children as $child) {
			$childId =
				($child instanceof $childClass)
					? $child->getId()
					: $child;

			$insertQuery =
				InsertQuery::create($this->getHelperTableName())
				->addFieldAndValue(
					$this->getParentFkColumn()->getFieldName(),
					new ScalarSqlValue($this->parent->getId())
				)
				->addFieldAndValue(
					$this->getChildFkColumn()->getFieldName(),
					new ScalarSqlValue($childId)
				);

			try {
				$this->childrenDao->sendQuery($insertQuery);
			}
			catch (UniqueViolationException $e) {
				//nothin'
			}
		}
	}

	/**
	 * Drop *:* associations where the specified children are assigned. Both the list of IDs and the
	 * list of children object (instanceof ChildObject) are supported
	 * @return int number of deleted associations
	 */
	protected function dropAssocByChildrenIds(array $children)
	{
		if (!empty($children)) {
			// cut the ids
			$deleteIdList = new SqlValueList();
			$childClass = $this->childrenMap->getLogicalSchema()->getEntityClassName();
			foreach ($children as $child) {
				$deleteIdList->add(
					new ScalarSqlValue(
						($child instanceof $childClass)
							? $child->getId()
							: $child
					)
				);
			}

			// create a query
			$deleteQuery = DeleteQuery::create($this->getHelperTableName())
				->setLogic(
					Expression::andBlock()
						->add(
							Expression::eq(
								$this->getParentFkColumn(),
								new ScalarSqlValue($this->parent->getId())
							)
						)
						->add(
							Expression::in(
								$this->getChildFkColumn(),
								$deleteIdList
							)
						)
				);

			// perform a query against children
			$this->childrenDao->sendQuery($deleteQuery);
		}
	}
}

?>