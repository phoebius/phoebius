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
 * Represents an expression chain
 * @ingroup DalExpression
 */
class ExpressionChain implements IExpression
{
	/**
	 * @var ExpressionChainPredicate
	 */
	private $predicate;

	/**
	 * @var array
	 */
	private $chain = array();

	/**
	 * @return ExpressionChain
	 */
	static function create(ExpressionChainPredicate $predicate, array $elements = array())
	{
		return new self ($predicate, $elements);
	}

	function __construct(ExpressionChainPredicate $predicate, array $elements = array())
	{
		$this->predicate = $predicate;
		foreach ($elements as $element) {
			$this->add($element);
		}
	}

	/**
	 * Adds the expression to the expression chain
	 * @return DalExpressionChain
	 */
	function add(IExpression $expression)
	{
		$this->chain[] = $expression;

		return $this;
	}

	/**
	 * @return boolean
	 */
	function isEmpty()
	{
		return empty($this->chain);
	}

	/**
	 * @return ExpressionChainPredicate
	 */
	function getPredicate()
	{
		return $this->predicate;
	}

	/**
	 * @return array
	 */
	function getChain()
	{
		return $this->chain;
	}

	/**
	 * @return BinaryExpression
	 */
	function toExpression(IExpressionSubjectConverter $converter)
	{
		$newChain = new self ($this->predicate);
		foreach ($this->chain as $item) {
			$newChain->chain[] = $item->toExpression($converter);
		}

		return $newChain;
	}

	/**
	 * @return BinaryDalExpression
	 */
	function toDalExpression()
	{
		return new DalExpressionChain($this);
	}
}

?>