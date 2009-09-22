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
	private $fk;

	final function __construct(OrmEntity $parent, OrmMap $children, $fkFieldName)
	{
		Assert::isScalar($fkFieldName);

		parent::__construct($parent, $children);

		$this->fk = $fkFieldName;
	}

	/**
	 * @return integer
	 */
	function dropList()
	{
		return $this->childrenDao->dropByCondition($this->generateLogic());
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
					$this->childrenMap->getPhysicalSchema()->getDBTableName()
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
		return Expression::andChain()
			->add(
				Expression::eq(
					$this->getFKColumn(),
					new ScalarSqlValue($this->parent->getId())
				)
			)
			->add(
				$this->getCondition()
			);
	}

	/**
	 * Overridden. Now uses silly algorithm of searching the column
	 * @return string
	 */
	protected function getFKColumn()
	{
		return new SqlColumn(
			$this->fk,
			$this->childrenMap->getPhysicalSchema()->getDBTableName()
		);
	}
}

?>