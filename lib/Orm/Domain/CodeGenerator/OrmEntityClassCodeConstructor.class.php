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
class OrmEntityClassCodeConstructor extends ClassCodeConstructor
{
	/**
	 * @return string
	 */
	function getClassName()
	{
		return $this->ormClass->getEntityName() . 'Entity';
	}

	/**
	 * @return boolean
	 */
	function isPublicEditable()
	{
		return true;
	}

	protected function getExtendsClassName()
	{
		return 'Auto' . $this->getClassName();
	}

	protected function getClassType()
	{
		return 'final';
	}

	/**
	 * @return void
	 */
	protected function findMembers()
	{
		$this->classMethods[] = <<<EOT
	/**
	 * @return {$this->getClassName()}
	 */
	static function getInstance()
	{
		return parent::instance(__CLASS__);
	}
EOT;
	}
}

?>