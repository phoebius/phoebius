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
 * @example SqlFunction.php
 * Using a SqlFunction
 */

/**
 * Sql function wrapper
 *
 * @ingroup Dal_DB_Sql
 */
final class SqlFunction implements ISqlValueExpression, ISelectQuerySource
{
	/**
	 * func (ALL expression)
	 */
	const AGGREGATE_ALL = 1;

	/**
	 * func (DISTINCT expression)
	 */
	const AGGREGATE_DISTINCT = 2;

	/**
	 * func (*)
	 */
	const AGGREGATE_WITH_NULLS = 3;

	/**
	 * @var string
	 */
	private $name;

	/**
	 * One of SqlFunction::AGGREGATE_* constants
	 * @var int
	 */
	private $aggregate;

	/**
	 * @var SqlValueExpressionList
	 */
	private $args;

	/**
	 * Creates an instance of {@link SqlFunction}
	 * @param string $name
	 * @return SqlFunction
	 */
	static function create($name)
	{
		return new self ($name);
	}

	/**
	 * @param string $name
	 */
	function __construct($name)
	{
		Assert::isScalar($name);

		$this->name = $name;
		$this->args = new SqlValueExpressionList();
	}

	/**
	 * Returns the name of the function
	 * @return string
	 */
	function getName()
	{
		return $this->name;
	}

	/**
	 * Returns the arguments set represented as {@link SqlValueExpressionList}
	 * @return SqlValueExpressionList
	 */
	function getArgs()
	{
		return $this->args;
	}

	/**
	 * Sets the new set of arguments
	 * @return SqlFunction an object itself
	*/
	function setArgs(SqlValueExpressionList $args)
	{
		$this->args = $args;

		return $this;
	}

	/**
	 * Adds an arg
	 * @return SqlFunction an object itself
	 */
	function addArg(ISqlValueExpression $arg)
	{
		$this->args->add($arg);

		return $this;
	}

	/**
	 * Drops a set of arguments
	 * @return SqlFunction an object itself
	 */
	function dropArgs()
	{
		$this->setArgs(new SqlValueExpressionList());

		return $this;
	}

	/**
	 * Invokes the aggregate across all input rows for which the given expression(s)
	 * yield non-null values
	 * @return SqlFunction an object itself
	 */
	function aggregateAll()
	{
		$this->aggregate = self::AGGREGATE_ALL;

		return $this;
	}

	/**
	 * Invokes the aggregate for all distinct non-null values of the expressions found in the
	 * input rows
	 * @return SqlFunction an object itself
	 */
	function aggregateDistinct()
	{
		$this->aggregate = self::AGGREGATE_DISTINCT;

		return $this;
	}

	/**
	 * Invokes the aggregate once for each input row regardless of null or non-null values.
	 * The set of arguments should be empty due '*' is used instead of arguments expansion
	 * @return SqlFunction an object itself
	 */
	function aggregateWithNulls()
	{
		$this->aggregate = self::AGGREGATE_WITH_NULLS;

		return $this;
	}

	/**
	 * Casts an object to the SQL dialect string
	 * @return string
	 */
	function toDialectString(IDialect $dialect)
	{
		$compiledSlices = array();

		$compiledSlices[] = $this->name;
		$compiledSlices[] = '(';

		if ($this->aggregate == self::AGGREGATE_WITH_NULLS) {
			Assert::isEmpty(
				$this->args->getList(),
				'drop out arguments when aggregating rows with nulls'
			);

			$compiledSlices[] = '*';
		}
		else {
			if ($this->aggregate == self::AGGREGATE_ALL) {
				Assert::isNotEmpty(
					$this->args->getList(),
					'set an aggregation expression as an argument'
				);

				$compiledSlices[] = 'ALL';
			}
			else if ($this->aggregate == self::AGGREGATE_DISTINCT) {
				Assert::isNotEmpty(
					$this->args->getList(),
					'set an aggregation expression as an argument'
				);

				$compiledSlices[] = 'DISTINCT';
			}

			$compiledSlices[] = $this->args->toDialectString($dialect);
		}

		$compiledSlices[] = ')';

		$compiledFunctionCall = join(' ', $compiledSlices);
		return $compiledFunctionCall;
	}
}

?>