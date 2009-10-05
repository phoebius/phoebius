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
abstract class OneToManyWorker extends ContainerWorker
{
	/**
	 * @var OrmProperty
	 */
	protected $referentialProperty;

	final function __construct(
			OrmEntity $parent,
			OrmClass $children,
			OrmProperty $referentialProperty
		)
	{
		$this->referentialProperty = $referentialProperty;

		parent::__construct($parent, $children);
	}

	/**
	 * @return integer
	 */
	function dropList()
	{
		return $this->children->getDao()->dropBy($this->getParentsChildrenExpression());
	}

	/**
	 * @return integer
	 */
	function getCount()
	{
		$row = $this->children->getDao()->getCustomRowByQuery(
			SelectQuery::create()
				->getExpression(
					SqlFunction::create('count')->aggregateWithNulls()
				)
				->from(
					$this->children->getPhysicalSchema()->getDBTableName()
				)
				->setExpression(
					$this->getParentsChildrenExpression()
				)
		);

		Assert::isNotEmpty($row);

		$count = reset($row);

		Assert::isNumeric($count);

		return $count;
	}

	/**
	 * @return IDalExpression
	 */
	protected function getParentsChildrenExpression()
	{
		return
			EntityQuery::create($this->children)
				->where(
					$this->referentialProperty,
					Expression::eq(
						$this->parent
					)
				);
	}
}

?>