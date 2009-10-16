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
 * @ingroup OrmCodeGenerator
 */
class OrmClassCodeConstructor extends ClassCodeConstructor
{
	/**
	 * @return boolean
	 */
	function isPublicEditable()
	{
		return true;
	}

	/**
	 * @return string
	 */
	function getClassName()
	{
		return $this->ormClass->getEntityName();
	}

	protected function getExtendsClassName()
	{
		return 'Auto' . $this->getClassName();
	}
}

?>