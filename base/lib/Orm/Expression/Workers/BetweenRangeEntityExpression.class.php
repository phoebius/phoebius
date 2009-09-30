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
 * Represents an range expression
 * @ingroup OrmExpression
 */
class BetweenRangeEntityExpression extends SingleRowEntityExpression
{
	function __construct($table, OrmProperty $property, BetweenRangeExpression $expression)
	{
		parent::__construct($table, $property, $expression);
	}

	/**
	 * @return BetweenRangeDalExpression
	 */
	function toDalExpression()
	{
		return DalExpression::between(
			$this->getSqlColumn(),
			$this->getSqlValue($this->expression->getFrom()),
			$this->getSqlValue($this->expression->getTo())

		);
	}
}

?>