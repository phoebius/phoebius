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
class BinaryEntityPropertyExpression extends SingleRowEntityPropertyExpression
{
	function __construct(EntityProperty $ep, BinaryExpression $expression)
	{
		parent::__construct($ep, $expression);
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