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
 * @ingroup Core_Expression
 */
class ExpressionChain implements ISubjective, IExpression
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
	 * @param ExpressionChainLogicalOperator logical operation to use when merging chain elements (conjunction or disjunction)
	 * @param array array of IExpression
	 */
	function __construct(ExpressionChainLogicalOperator $logicalOperator, array $elements = array())
	{
		$this->logicalOperator = $logicalOperator;
		foreach ($elements as $element) {
			$this->add($element);
		}
	}

	/**
	 * Adds the expression to the expression chain
	 * @return ExpressionChain
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

	function toSubjected(ISubjectivity $object)
	{
		$newChain = new self ($this->logicalOperator);
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

			$out = join($this->logicalOperator->toDialectString($dialect), $slices);

			return $out;
		}

		return '';
	}
}

?>