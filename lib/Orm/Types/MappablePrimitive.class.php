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
 * For future mappings. Orm.Map will map those primitve property types with SQL types via
 * DB dialect provider
 * @see NHibernate/Dialect/TypeNames.cs NHibernate.Dialect.TypeNames
 * @see NHibernate/Dialect/PostgreSQLDialect.cs NHibernate.Dialect.PostgreSQLDialect
 * @ingroup Orm_Types
 */
class MappablePrimitive extends Enumeration
{
	const STRING = 'string';
	const INTEGER = 'integer';
	const FLOAT = 'float';
	const NUMERIC = 'numeric';
	const BOOLEAN = 'boolean';
}

?>