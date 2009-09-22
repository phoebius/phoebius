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

	final function __construct(OrmEntity $parent, OrmClass $children, OrmProperty $referentialProperty)
	{
		$this->referentialProperty = $referentialProperty;

		parent::__construct($parent, $children);
	}

	/**
	 * @return integer
	 */
	function dropList()
	{
		return $this->children->getDao()->dropByCondition($this->generateLogic());
	}

	/**
	 * @return integer
	 */
	function getCount()
	{
		$row = $this->childrenDao->getCustomRowByQuery(
			SelectQuery::create()
				->getExpression(
					SqlFunction::create('count')->aggregateWithNulls()
				)
				->from(
					$this->children->getPhysicalSchema()->getDBTableName()
				)
				->setCondition(
					$this->generateLogic()
				)
		);

		Assert::isNotEmpty($row);

		$count = reset($row);

		Assert::isNumeric($count);

		return $count;
	}

	/**
	 * @return ISqlLogicalExpression
	 */
	protected function generateLogic()
	{
		$query = $this->children->getOrmQuery();

		$expression = Expression::andChain();
		$table = $this->children->getPhysicalSchema()->getDBTableName();
		$rawValue = $this->referentialProperty->getType()->makeRawValue($this->parent->getId());

		foreach ($query->makeColumnValue($property, $rawValue) as $column => $sqlValue) {
			$expression->add(
				Expression::eq(
					new SqlColumn(
						$column,
						$table
					),
					$sqlValue
				)
			);
		}

		return $expression;
	}
}

?>