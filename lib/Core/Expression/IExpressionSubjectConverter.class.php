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
interface IExpressionSubjectConverter
{
	/**
	 * @param mixed subject to convert
	 * @param IExpression subject container (for building call tree while resolving)
	 * @return IExpression
	 */
	function convert($subject, IExpression $object);
}

?>