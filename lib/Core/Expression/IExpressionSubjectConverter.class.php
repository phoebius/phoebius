<?php
/* ***********************************************************************************************
 *
 * Phoebius Framework
 *
 * **********************************************************************************************
 *
 * Copyright (c) 2009 phoebius.org
 *
 * This program is free software; you can redistribute it and/or modify it under the terms
 * of the GNU Lesser General Public License as published by the Free Software Foundation;
 * either version 3 of the License, or (at your option) any later version.
 *
 * You should have received a copy of the GNU Lesser General Public License along with
 * this program; if not, see <http://www.gnu.org/licenses/>.
 *
 ************************************************************************************************/

/**
 * Describes the expression subject converter.
 *
 * This converter is used to
 *
 * @ingroup Core_Expression
 */
interface IExpressionSubjectConverter
{
	/**
	 * @param mixed subject to convert
	 * @param IExpression subject container
	 * @return mixed converted subject
	 */
	function convert($subject, IExpression $object);
}

?>