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
 * Represents a postfix unary expression
 * @ingroup OrmExpression
 */
class UnaryPostfixEntityExpression extends SingleRowEntityExpression
{
	function __construct($table, OrmProperty $property, UnaryPostfixExpression $expression)
	{
		parent::__construct($table, $property, $expression);
	}

	/**
	 * @return IDalExpression
	 */
	function toDalExpression()
	{
		return new UnaryPostfixDalExpression(
			$this->getSqlColumn(),
			$this->getExpression()
		);
	}
}

?>