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
 * @ingroup Dal_DB_Expression
 */
class DalExpressionChain implements IDalExpression
{
	/**
	 * @var array
	 */
	private $chain;

	/**
	 * @var ExpressionChainLogicalOperator
	 */
	private $logicalOperator;

	function __construct(ExpressionChain $expressionChain)
	{
		$this->logicalOperator = $expressionChain->getLogicalOperator();

		foreach ($expressionChain->getChain() as $item) {
			$this->chain[] = $item->toDalExpression();
		}
	}

	/**
	 * Casts an object to the SQL dialect string
	 * @return string|null
	 */
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

		//nothin'
		return '';
	}
}

?>