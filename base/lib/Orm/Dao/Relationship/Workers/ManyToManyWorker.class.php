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
	 * @var string
	 */
	private $helperTableName;

	/**
	 * @var SqlColumn
	 */
	private $parentFkColumn;

	/**
	 * @var SqlColumn
	 */
	private $childFkColumn;

	final function __construct(
			OrmEntity $parent,
			OrmMap $children,
			ManyToManyContainerPropertyType $mtmType
		)
	{
		Assert::isScalar($helperTableName);

		$this->parent = $parent;
		$this->childrenMap = $children;
		$this->childrenDao = $children->getDao();

		Assert::notImplemented('use proxy data here (mtmType)');

		$this->helperTableName = $helperTableName;
		$this->parentFkColumn = $parentFkColumn;
		$this->childFkColumn = $childFkColumn;
	}

	/**
	 * @return SqlColumn
	 */
	protected function getParentFkColumn()
	{
		return $this->parentFkColumn;
	}

	/**
	 * @return SqlColumn
	 */
	protected function getChildFkColumn()
	{
		return $this->childFkColumn;
	}

	/**
	 * @return string
	 */
	protected function getHelperTableName()
	{
		return $this->helperTableName;
	}

	/**
	 * @return integer
	 */
	function dropList()
	{
		$deleteQuery = new DeleteQuery($this->helperTableName);

		$deleteQuery->setCondition(
			Expression::in(
				$this->getParentFkColumn(),
				new ScalarSqlValue($this->parent->getId())
			)
		);

		$count = $this->childrenDao->sendQuery($deleteQuery);

		return (int)$count;
	}

	/**
	 * @return IDalExpression
	 */
	private function getJoinLogic()
	{
		return Expression::andChain()
			->add(
				Expression::eq(
					$this->parentFkColumn,
					new ScalarSqlValue($this->parent->getId())
				)
			)
			->add(
				Expression::eq(
					$this->childFkColumn,
					new SqlColumn(
						$this->childrenMap->getPhysicalSchema()->getIdentifierFieldName(),
						$this->childrenMap->getPhysicalSchema()->getDBTableName()
					)
				)
			)
			->add(
				$this->getCondition()
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
		$selectQuery->setCondition($this->getJoinLogic());

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