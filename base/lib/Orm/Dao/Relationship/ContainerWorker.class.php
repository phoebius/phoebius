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
 * @ingroup RelationshipDao
 */
abstract class ContainerWorker
{
	/**
	 * @var OrmEntity
	 */
	protected $parent;

	/**
	 * @var ISqlLogicalExpression|null
	 */
	private $condition = null;

	/**
	 * @var OrmClass
	 */
	protected $children;

	/**
	 * @return void
	 */
	abstract function syncronizeObjects(array $insert, array $update, array $delete);

	/**
	 * @return array
	 */
	abstract function getList();

	/**
	 * @return integer
	 */
	abstract function dropList();

	function __construct(OrmEntity $parent, OrmClass $children)
	{
		$this->parent = $parent;
		$this->children = $children;
	}

	/**
	 * @return ContainerWorker an object itself
	 */
	function setCondition(ISqlLogicalExpression $condition = null)
	{
		$this->condition = $condition;

		return $this;
	}

	/**
	 * @return ISqlLogicalExpression
	 */
	function getCondition()
	{
		if (!$this->condition) {
			$this->condition = new NullExpression();
		}

		return $this->condition;
	}
}

?>