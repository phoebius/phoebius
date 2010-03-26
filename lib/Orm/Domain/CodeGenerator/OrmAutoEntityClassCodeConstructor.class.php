<?php
/* ***********************************************************************************************
 *
 * Phoebius Framework
 *
 * **********************************************************************************************
 *
 * Copyright (c) 2009 Scand Ltd.
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
 * An abstract representation of helper lass for accessing auxiliary structures of ORM-related entity
 *
 * @ingroup Orm_Domain_CodeGenerator
 */
class OrmAutoEntityClassCodeConstructor extends OrmRelatedClassCodeConstructor
{
	function getClassName()
	{
		return 'Auto' . $this->ormClass->getEntityName() . 'Entity';
	}

	protected function getClassType()
	{
		return 'abstract';
	}

	function isPublicEditable()
	{
		return false;
	}

	protected function getExtendsClassName()
	{
		return 'LazySingleton';
	}

	protected function getImplementsInterfaceNames()
	{
		return array(
			$this->ormClass->hasDao()
				? 'IQueryable'
				: 'IMappable'
		);
	}

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
			$this->classProperties[] = <<<EOT
	/**
	 * @var IOrmEntityAccessor
	 */
	private \$dao;
EOT;

			//
			// FIXME allow entity to hav custom db-schema
			//
			$this->classMethods[] = <<<EOT
	/**
	 * @return IOrmEntityAccessor
	 */
	function getDao()
	{
		if (!\$this->dao) {
			\$this->dao = new RdbmsDao(
				DBPool::getDefault(),
				\$this
			);
		}

		return \$this->dao;
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