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
 * Represents a unary prefix expression
 * @ingroup OrmExpression
 */
class PrefixUnaryEntityPropertyExpression extends SingleRowEntityPropertyExpression
{
	function __construct($table, OrmProperty $property, PrefixUnaryExpression $expression)
	{
		parent::__construct($table, $property, $expression);
	}

	/**
	 * @return IDalExpression
	 */
	function toDalExpression()
	{
		return new PrefixUnaryDalExpression(
			new PrefixUnaryExpression(
				$this->getExpression()->getPredicate(),
				$this->getSqlColumn()
			)
		);
	}
}

?>