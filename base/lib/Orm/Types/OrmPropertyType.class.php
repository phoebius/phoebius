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
 * @ingroup OrmTypes
 */
abstract class OrmPropertyType implements IPropertyMappable, IPropertyStructurized
{
	/**
	 * @return string
	 */
	abstract function getImplClass();

	/**
	 * @return mixed
	 */
	function getDefaultValue()
	{
		Assert::isUnreachable('no default value');
	}

	/**
	 * @return boolean
	 */
	function hasDefaultValue()
	{
		return false;
	}
}

?>