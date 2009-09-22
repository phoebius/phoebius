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
 * Represents the SQL ORDER BY chain
 * @ingroup Sql
 */
final class SqlOrderChain extends TypedValueList implements ISqlCastable
{
	/**
	 * Create an instance of {@link SqlOrderChain}
	 * @var array of {@link SqlOrderExpression}
	 * @return SqlOrderChain
	 */
	static function create(array $expressions = array())
	{
		return new self ($expressions);
	}

	/**
	 * Adds a {@link SqlOrderExpression} order expression
	 * @return SqlOrderChain
	 */
	function add(SqlOrderExpression $expression)
	{
		$this->append($expression);
	}

	/**
	 * Drops the ascending logic in all order expressions and sets the ASC logic to the last one
	 * expression
	 * @return SqlOrderChain an object itself
	 */
	function asc()
	{
		if ($this->getCount()) {
			foreach ($this->getList() as $expression) {
				$expression->setNone();
			}

			end($this->getList())->setAsc();
		}

		return $this;
	}

	/**
	 * Drops the ascending logic in all order expressions and sets the DESC logic to the last one
	 * expression
	 * @return SqlOrderChain
	 */
	function desc()
	{
		if ($this->getCount()) {
			foreach ($this->getList() as $expression) {
				$expression->setNone();
			}

			end($this->getList())->setDesc();
		}

		return $this;
	}

	/**
	 * Casts an object to the SQL dialect string
	 * @return string
	 */
	function toDialectString(IDialect $dialect)
	{
		$compiledSlices = array();

		if ($this->getCount() > 0) {
			foreach($this->getList() as $orderByExpression) {
				$compiledSlices[] = $orderByExpression->toDialectString($dialect);
			}
		}

		$compiledString = 'ORDER BY ' . join(', ', $compiledSlices);

		return $compiledString;
	}

	/**
	 * Determines whether the specified value is of valid type supported by the list implementation
	 * @return boolean
	 */
	protected function isValueOfValidType($value)
	{
		return ($value instanceof SqlOrderExpression);
	}
}

?>