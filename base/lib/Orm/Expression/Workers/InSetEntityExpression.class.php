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
 * Represents the IN expression used in query logic
 * @ingroup OrmExpression
 */
class InSetEntityExpression extends SingleRowEntityExpression
{
	function __construct($table, OrmProperty $property, InSetExpression $expression)
	{
		parent::__construct($table, $property, $expression);
	}

	/**
	 * @return IDalExpression
	 */
	function toDalExpression()
	{
		$sqlValues = array();
		foreach ($this->getExpression()->getValue() as $value) {
			$sqlValues[] = $this->getSqlValue($value);
		}

		return new InSetDalExpression(
			$this->getSqlColumn(),
			new InSetExpression(
				$sqlValues,
				$this->getExpression()->getPredicate()
			)
		);
	}
}

?>