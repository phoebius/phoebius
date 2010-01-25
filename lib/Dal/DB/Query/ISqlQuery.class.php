<?php
/* ***********************************************************************************************
 *
 * Phoebius Framework
 *
 * **********************************************************************************************
 *
 * Copyright (c) 2009 Scand Ltd.
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
 * Defines an object that represents a database query.
 * @ingroup Dal_DB_Query
 */
interface ISqlQuery extends ISqlCastable
{
	/**
	 * Gets the database values that should be mapped to database-level placeholers that are
	 * presented in a query.
	 *
	 * This can be extremely useful when using database placeholders and query preparation
	 * over high-load DB servers with persistent connections.
	 *
	 * @return array of scalar
	 */
	function getPlaceholderValues(IDialect $dialect);
}

?>