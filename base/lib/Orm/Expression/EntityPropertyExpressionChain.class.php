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
	 * @var ExpressionChainPredicate
	 */
	private $expressionChainPredicate;

	/**
	 * @var array of {@link EntityQuery}
	 */
	private $children = array();

	/**
	 * @return EntityQuery
	 */
	static function create(ExpressionChainPredicate $expressionChainPredicate = null)
	{
		return new self ($expressionChainPredicate);
	}

	function __construct(ExpressionChainPredicate $expressionChainPredicate = null)
	{
		$this->expressionChainPredicate =
			$expressionChainPredicate
				? $expressionChainPredicate
				: ExpressionChainPredicate::conditionAnd();
	}

	/**
	 * @return ExpressionChainPredicate
	 */
	function getPredicate()
	{
		return $this->expressionChainPredicate;
	}

	/**
	 * @return EntityExpressionChain
	 */
	function setAndBlock()
	{
		$this->expressionChainPredicate = ExpressionChainPredicate::conditionAnd();

		return $this;
	}

	/**
	 * @return EntityExpressionChain
	 */
	function setOrBlock()
	{
		$this->expressionChainPredicate = ExpressionChainPredicate::conditionOr();

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
		$chain = new DalExpressionChain($this->expressionChainPredicate);
		foreach ($this->children as $child) {
			$chain->add($child->toDalExpression());
		}

		return $chain;
	}
}

?>