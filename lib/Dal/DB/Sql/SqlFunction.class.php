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
 * Sql function wrapper
 *
 * @ingroup Dal_DB_Sql
 */
class SqlFunction implements ISqlValueExpression, ISqlSelectable, ISubjective
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
	 * @var arrau
	 */
	private $args;

	/**
	 * @return SqlFunction
	 */
	static function create($name)
	{
		$args = func_get_args();
		array_shift($args);

		$me = new self ($name);
		$me->args = $args;

		return $me;
	}

	/**
	 * @param string $name
	 */
	function __construct($name)
	{
		Assert::isScalar($name);

		$args = func_get_args();
		array_shift($args);

		$this->name = $name;
		$this->args = $args;
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
	 * Returns the arguments set represented as {@link SqlValueExpressionArray}
	 * @return SqlValueExpressionArray
	 */
	function getArgs()
	{
		return $this->args;
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

		$args = new SqlValueExpressionArray($this->args);

		$compiledSlices[] = $this->name;
		$compiledSlices[] = '(';

		if ($this->aggregate == self::AGGREGATE_WITH_NULLS) {
			Assert::isTrue(
				$args->isEmpty(),
				'drop out arguments when aggregating rows with nulls'
			);

			$compiledSlices[] = '*';
		}
		else {
			if ($this->aggregate == self::AGGREGATE_ALL) {
				Assert::isFalse(
					$args->isEmpty(),
					'set an aggregation expression as an argument'
				);

				$compiledSlices[] = 'ALL';
			}
			else if ($this->aggregate == self::AGGREGATE_DISTINCT) {
				Assert::isFalse(
					$args->isEmpty(),
					'set an aggregation expression as an argument'
				);

				$compiledSlices[] = 'DISTINCT';
			}

			$compiledSlices[] = $args->toDialectString($dialect);
		}

		$compiledSlices[] = ')';

		$compiledFunctionCall = join(' ', $compiledSlices);

		return $compiledFunctionCall;
	}

	function toSubjected(ISubjectivity $object)
	{
		$clone = new self ($this->name);

		foreach ($this->args as $arg) {
			$clone->args[] = $object->subject($arg, $this);
		}

		return $clone;
	}
}

?>