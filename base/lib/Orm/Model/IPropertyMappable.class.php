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
 * Maps physical value to logical value, and vice versa.
 * @ingroup OrmModel
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

	/**
	 * @return IEntityPropertyExpression
	 */
	function getEntityPropertyExpression(
			$table,
			OrmProperty $ormProperty,
			IExpression $expression
	);
}

?>