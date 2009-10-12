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
class OrmAutoEntityClassCodeConstructor extends ClassCodeConstructor
{
	/**
	 * @return string
	 */
	function getClassName()
	{
		return 'Auto' . $this->ormClass->getEntityName() . 'Entity';
	}

	/**
	 * @return boolean
	 */
	function isPublicEditable()
	{
		return false;
	}

	/**
	 * @return string
	 */
	protected function getExtendsClassName()
	{
		return 'LazySingleton';
	}

	/**
	 * @return string
	 */
	protected function getImplementsInterfaceNames()
	{
		return array(
			$this->ormClass->hasDao()
				? 'IQueryable'
				: 'IMappable'
		);
	}

	/**
	 * @return void
	 */
	protected function findMembers()
	{
		$this->classMethods[] = <<<EOT
	/**
	 * @return IOrmEntityMapper
	 */
	function getMap()
	{
		return new OrmMap(\$this->getLogicalSchema());
	}
EOT;

		$this->classMethods[] = <<<EOT
	/**
	 * @return ILogicallySchematic
	 */
	function getLogicalSchema()
	{
		return new {$this->ormClass->getEntityName()}EntityLogicalSchema;
	}
EOT;

		if ($this->ormClass->hasDao()) {
			$this->classMethods[] = <<<EOT
	/**
	 * @return IOrmEntityAccessor
	 */
	function getDao()
	{
		return new RdbmsDao(\$this->getPhysicalSchema());
	}
EOT;

			$this->classMethods[] = <<<EOT
	/**
	 * @return IPhysicallySchematic
	 */
	function getPhysicalSchema()
	{
		return new {$this->ormClass->getEntityName()}EntityPhysicalSchema;
	}
EOT;
		}
	}
}

?>