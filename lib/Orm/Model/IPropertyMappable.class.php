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
 * Maps physical value to logical value, and vice versa.
 * @ingroup Orm_Model
 */
interface IPropertyMappable
{
	/**
	 * @return mixed
	 */
	function makeValue(array $rawValue, FetchStrategy $fetchStrategy);

	/**
	 * @return array
	 */
	function makeValueSet(array $rawValueSet, FetchStrategy $fetchStrategy);

	/**
	 * @return array
	 */
	function makeRawValue($value);

	/**
	 * @return boolean
	 */
	function isNullable();

	/**
	 * @return boolean
	 */
	function hasDefaultValue();

	/**
	 * @throws OrmModelPropertyException if property cannot have the default value
	 * @return mixed
	 */
	function getDefaultValue();

//	/**
//	 * @return IEntityPropertyExpression
//	 */
//	function getEntityPropertyExpression(
//			$table,
//			OrmProperty $ormProperty,
//			IExpression $expression
//	);
}

?>