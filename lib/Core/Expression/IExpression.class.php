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
interface IExpression
{
	/**
	 * @obsolete
	 * @return ExpressionType
	 */
	//function getExpressionType();

	/**
	 * @return IExpression
	 */
	function toExpression(IExpressionSubjectConverter $converter);

	/**
	 * @return IDalExpression
	 */
	function toDalExpression();
}

?>