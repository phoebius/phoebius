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
 * @ingroup OrmExpression
 */
final class EntityPropertyExpressionChain implements IEntityPropertyExpression
{
	/**
	 * @var ExpressionChainLogicalOperator
	 */
	private $expressionChainLogicalOperator;

	/**
	 * @var array of {@link EntityQuery}
	 */
	private $children = array();

	/**
	 * @return EntityQuery
	 */
	static function create(ExpressionChainLogicalOperator $expressionChainLogicalOperator = null)
	{
		return new self ($expressionChainLogicalOperator);
	}

	function __construct(ExpressionChainLogicalOperator $expressionChainLogicalOperator = null)
	{
		$this->expressionChainLogicalOperator =
			$expressionChainLogicalOperator
				? $expressionChainLogicalOperator
				: ExpressionChainLogicalOperator::conditionAnd();
	}

	/**
	 * @return ExpressionChainLogicalOperator
	 */
	function getLogicalOperator()
	{
		return $this->expressionChainLogicalOperator;
	}

	/**
	 * @return EntityExpressionChain
	 */
	function setAndBlock()
	{
		$this->expressionChainLogicalOperator = ExpressionChainLogicalOperator::conditionAnd();

		return $this;
	}

	/**
	 * @return EntityExpressionChain
	 */
	function setOrBlock()
	{
		$this->expressionChainLogicalOperator = ExpressionChainLogicalOperator::conditionOr();

		return $this;
	}

	function add(IEntityPropertyExpression $entityExpression)
	{
		$this->children[] = $entityExpression;

		return $this;
	}

	/**
	 * @return IDalExpression
	 */
	function toDalExpression()
	{
		$chain = new DalExpressionChain($this->expressionChainLogicalOperator);
		foreach ($this->children as $child) {
			$chain->add($child->toDalExpression());
		}

		return $chain;
	}
}

?>