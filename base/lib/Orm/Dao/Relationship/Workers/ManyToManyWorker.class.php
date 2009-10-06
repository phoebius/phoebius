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
				// maps proxy.parent_id<->parent.id as value
				EntityQuery::create($this->mtm->getProxy())
					->where(
						$this->mtm->getContainerProxyProperty(),
						Expression::eq(
							$this->parent
						)
					)
			)
			->add(
				// maps proxy.child_id<->child.id
				EntityQuery::create($this->mtm->getProxy())
					->where(
						$this->mtm->getEncapsulantProxyProperty(),
						Expression::eq(
							EntityQuery::create($this->children)
								->getPropertyClause($this->children->getIdentifier())
						)
					)
			)
			->add(
				// additional criterion
				$this->getExpression()
			);
	}

	/**
	 * @return array
	 */
	protected function getChildrenIds()
	{
		$query = EntityQuery::create($this->mtm->getProxy())
			->where(

			)
			->andWhere(

			)
			->andWhere(

			);


		$childrenTableName = $this->children->getPhysicalSchema()->getDBTableName();

		$selectQuery = new SelectQuery();
		$selectQuery->from($this->getHelperTableName());
		$selectQuery->from($childrenTableName);
		$selectQuery->get(
			$this->children->getPhysicalSchema()->getIdentifierFieldName(),
			null,
			$childrenTableName
		);
		$selectQuery->setExpression($this->getJoinLogic());

		$rows = $this->children->getCustomRowsByQuery(
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
		$proxyDao = $this->mtm->getProxy()->getDao();

		$proxyObjProto = $this->mtm->getProxy()->getLogicalSchema()->getNewEntity();
		$proxyObjProto->{$this->mtm->getContainerProxyProperty()->getSetter()}($this->parent);

		$childSetter = $this->mtm->getEncapsulantProxyProperty()->getSetter();

		foreach ($children as $child) {
			$proxyObj = clone $proxyObjProto;
			$proxyObj->{$childSetter}($child);

			$proxyDao->save($proxyObj);
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
			$this->mtm->getProxy()->getDao()->dropBy(
				EntityQuery::create($this->mtm->getProxy())
					->where(
						$this->mtm->getContainerProxyProperty(),
						Expression::eq($this->parent)
					)
					->where(
						$this->mtm->getEncapsulantProxyProperty(),
						Expression::in(
							$children
						)
					)
			);
		}
	}
}

?>