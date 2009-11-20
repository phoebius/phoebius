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
 * PHP example:
 * @code
 * $intarr = new RawSqlType('int8[]');
 * @endcode
 *
 * Raw sql type definition inside domain schema.sql:
 * @code
 * <!-- a property that is generated at database level -->
 * <property name="getChildCount" column="child_count" visibility="readonly" type="RawSqlType { definition='int4 not null default 0 check(child_count >= 0)' }">
 *
 * <!-- a property that is used completely at db level -->
 * <property name="childIds" column="child_ids" visibility="transparent" type="RawSqlType { definition='int8[]' }">
 * @endcode
 *
 * @ingroup
 */
class RawSqlType implements ISqlType
{
	private $definition;

	/**
	 * @param $definition sql definition of a column
	 */
	function __construct($definition)
	{
		Assert::isScalar($definition);

		$this->definition = $definition;
	}

	function toDialectString(IDialect $dialect)
	{
		return $this->definition;
	}
}

?>