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

	/**
	 * @return string
	 */
	function toPhpCodeCall()
	{
		return join('', array(
			'new ',
			get_class($this),
			'(',
			join(',', $this->getCtorArgumentsPhpCode()),
			')'
		));
	}

	protected function getCtorArgumentsPhpCode()
	{
		return array();
	}
}

?>