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
 * Represents an abstract logical operator used for connecting subjects in expression trees
 * @ingroup ExpressionLogicalOperators
 */
abstract class LogicalOperator extends Enumeration implements ISqlCastable
{
	/**
	 * Casts an object to the SQL dialect string
	 * @return string
	 */
	function toDialectString(IDialect $dialect)
	{
		return $this->getValue();
	}
}

?>