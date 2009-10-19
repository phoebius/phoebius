<?php
/* ***********************************************************************************************
 *
 * Phoebius Framework
 *
 * **********************************************************************************************
 *
 * Copyright (c) 2009 phoebius.org
 *
 * This program is free software; you can redistribute it and/or modify it under the terms
 * of the GNU Lesser General Public License as published by the Free Software Foundation;
 * either version 3 of the License, or (at your option) any later version.
 *
 * You should have received a copy of the GNU Lesser General Public License along with
 * this program; if not, see <http://www.gnu.org/licenses/>.
 *
 ************************************************************************************************/

/**
 * Represents an expression chain
 * @ingroup Core_Expression
 */
class ExpressionChain implements IExpression
{
	/**
	 * @var ExpressionChainLogicalOperator
	 */
	private $logicalOperator;

	/**
	 * @var array
	 */
	private $chain = array();

	/**
	 * @return ExpressionChain
	 */
	static function create(ExpressionChainLogicalOperator $logicalOperator, array $elements = array())
	{
		return new self ($logicalOperator, $elements);
	}

	function __construct(ExpressionChainLogicalOperator $logicalOperator, array $elements = array())
	{
		$this->logicalOperator = $logicalOperator;
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
	 * @return ExpressionChainLogicalOperator
	 */
	function getLogicalOperator()
	{
		return $this->logicalOperator;
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
		$newChain = new self ($this->logicalOperator);
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