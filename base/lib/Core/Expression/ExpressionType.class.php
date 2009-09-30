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
 * @ingroup BaseExpression
 */
class ExpressionType extends Enumeration
{
	const BINARY = 1;
	const BETWEEN = 2;
	const IN_SET = 3;
	const PREFIX_UNARY = 4;
	const UNARY_POSTFIX = 5;
}

?>