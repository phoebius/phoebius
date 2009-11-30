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
 * Represents the chain of IExpression
 *
 * Example:
 * @code
 * // "id" IN (1, 2) OR "id" IS NULL
 * Expression::disjunction(
 * 	Expression::inSet("id", array(1, 2),
 * 	Expression::isNull("id")
 * );
 * @endcode
 * @ingroup Dal_Expression
 */
class ExpressionChain implements ISubjective, IExpression
{
	/**
	 * @var ExpressionChainLogicalOperator
	 */
	private $operator;

	/**
	 * @var array
	 */
	private $chain = array();

	/**
	 * @param ExpressionChainLogicalOperator $operator logical operation to use
	 * 													when merging chain elements
	 * 													(conjunction or disjunction)
	 * @param array $elements array of IExpression
	 */
	function __construct(ExpressionChainLogicalOperator $operator, array $elements = array())
	{
		$this->operator = $operator;
		foreach ($elements as $element) {
			$this->add($element);
		}
	}

	/**
	 * Adds the expression to the expression chain
	 * @param IExpression $expression
	 * @return ExpressionChain itself
	 */
	function add(IExpression $expression)
	{
		$this->chain[] = $expression;

		return $this;
	}

	/**
	 * Determines whether the chain is empty or not
	 *
	 * @return boolean
	 */
	function isEmpty()
	{
		return empty($this->chain);
	}

	/**
	 * Gets the IExpression object containing within the chain
	 *
	 * @return array of IExpression
	 */
	function getChain()
	{
		return $this->chain;
	}

	function toSubjected(ISubjectivity $object)
	{
		$newChain = new self ($this->operator);
		foreach ($this->chain as $item) {
			$newChain->chain[] = $item->toSubjected($object);
		}

		return $newChain;
	}

	function toDialectString(IDialect $dialect)
	{
		if (!empty($this->chain)) {
			$slices = array();

			foreach ($this->chain as $expression) {
				$sqlExpression = $expression->toDialectString($dialect);

				if (empty($sqlExpression)) {
					continue;
				}

				$slices[] = ' ( ' . $sqlExpression . ' ) ';
			}

			$out = join($this->operator->toDialectString($dialect), $slices);

			return $out;
		}

		return '';
	}
}

?>