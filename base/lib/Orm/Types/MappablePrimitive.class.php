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
 * For future mappings. Orm.Map will map those primitve property types with SQL types via
 * DB dialect provider
 * @see NHibernate/Dialect/TypeNames.cs NHibernate.Dialect.TypeNames
 * @see NHibernate/Dialect/PostgreSQLDialect.cs NHibernate.Dialect.PostgreSQLDialect
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