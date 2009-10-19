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
 * Must be in sync with OrmPropertyType::$entityExpressionWorkers.
 * @ingroup Core_Expression
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