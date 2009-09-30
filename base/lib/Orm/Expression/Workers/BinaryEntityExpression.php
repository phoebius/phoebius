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
 * FIXME: reimplement this class to support multi-field properties
 * Represents binary expression
 * @ingroup OrmExpression
 */
class BinaryEntityExpression extends SingleRowEntityExpression
{
	function __construct($table, OrmProperty $property, BinaryExpression $expression)
	{
		parent::__construct($table, $property, $expression);
	}

	/**
	 * @return IDalExpression
	 */
	function toDalExpression()
	{
		return new BinaryDalExpression(
			$this->getSqlColumn(),
			new BinaryExpression(
				$this->getExpression()->getPredicate(),
				$this->getSqlValue($this->getExpression()->getValue())
			)
		);
	}
}

?>