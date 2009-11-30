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
 * Represents a sql join method
 *
 * @ingroup Dal_DB_Query
 * @aux
 */
final class SqlJoinMethod extends Enumeration implements ISqlCastable
{
	const LEFT = 'LEFT';
	const LEFT_OUTER = 'LEFT OUTER';
	const RIGHT = 'RIGHT';
	const RIGHT_OUTER = 'RIGHT OUTER';
	const INNER = 'INNER';
	const CROSS = 'CROSS';

	function toDialectString(IDialect $dialect)
	{
		return $this->getValue() . ' JOIN';
	}
}

?>