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
class SqlFunction implements ISqlValueExpression, ISubjective
{
	/**
	 * func (ALL expression)
	 */
	const AGGREGATE_ALL = 'ALL';

	/**
	 * func (DISTINCT expression)
	 */
	const AGGREGATE_DISTINCT = 'DISTINCT';

	/**
	 * func (*)
	 */
	const AGGREGATE_WITH_NULLS = '*';

	/**
	 * @var string
	 */
	private $name;

	/**
	 * One of SqlFunction::AGGREGATE_* constants
	 */
	private $aggregate;

	/**
	 * @var array
	 */
	private $args;

	/**
	 * Function that invokes the aggregate once for each input row regardless of null or non-null
	 * values.
	 * @param mixed $name name of the function
	 * @return SqlFunction
	 */
	static function aggregateWithNulls($name)
	{
		$func = new self ($name);
		$func->aggregate = self::AGGREGATE_WITH_NULLS;

		return $func;
	}

	/**
	 * Function that invokes the aggregate for all distinct non-null values of the expressions
	 * found in the input rows
	 * @param string $name name of the function
	 * @param mixed $field field to aggregate
	 * @return SqlFunction
	 */
	static function aggregateDistinct($name, $field)
	{
		$func = new self ($name, $field);
		$func->aggregate = self::AGGREGATE_DISTINCT;

		return $func;
	}

	/**
	 * Function that invokes the aggregate across all input rows for which the given expression(s)
	 * yield non-null values
	 * @param string $name name of the function
	 * @param mixed $field field to aggregate
	 * @return SqlFunction
	 */
	static function aggregateAll($name, $field)
	{
		$func = new self ($name, $field);
		$func->aggregate = self::AGGREGATE_ALL;

		return $func;
	}

	/**
	 * @param string $name name of the function
	 * @param mixed ... optional arguments to be passed to SQL function
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
	 * Gets the name of the function
	 * @return string
	 */
	function getName()
	{
		return $this->name;
	}

	/**
	 * Gets the function arguments
	 * @return array
	 */
	function getArgs()
	{
		return $this->args;
	}

	function toDialectString(IDialect $dialect)
	{
		$compiledSlices = array();

		$compiledSlices[] = $this->name;
		$compiledSlices[] = '(';

		if ($this->aggregate) {
			$compiledSlices[] = $this->aggregate;
		}

		$args = new SqlValueExpressionArray($this->args);
		$compiledSlices[] = $args->toDialectString($dialect);

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