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
 * @ingroup Orm_Domain_CodeGenerator
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
	 * @return string final|abstract|null
	 */
	protected function getClassType()
	{
		return 'abstract';
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
			//
			// FIXME allow entity to hav custom db-schema
			//
			$this->classMethods[] = <<<EOT
	/**
	 * @return IOrmEntityAccessor
	 */
	function getDao()
	{
		return new RdbmsDao(
			DBPool::getDefault(),
			\$this
		);
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