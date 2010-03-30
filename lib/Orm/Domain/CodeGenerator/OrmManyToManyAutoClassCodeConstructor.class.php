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
 * Many-to-many worker generator
 *
 * @ingroup Orm_Domain_CodeGenerator
 */
class OrmManyToManyAutoClassCodeConstructor extends OrmContainerClassCodeConstructor
{
	function isPublicEditable()
	{
		return false;
	}

	function getClassName()
	{
		return $this->propertyType->getAutoContainerClassName($this->ormProperty);
	}

	protected function findMembers()
	{
		$this->classMethods[] = <<<EOT
	function __construct({$this->ormClass->getName()} \$parent, \$readOnly = true)
	{
		parent::__construct(
			\$parent,
			{$this->propertyType->getEncapsulant()->getEntityName()}::orm(),
			{$this->ormClass->getEntityName()}::orm()->getLogicalSchema()->getProperty('{$this->ormProperty->getName()}'),
			\$readOnly
		);
	}
EOT;
	}

	protected function getExtendsClassName()
	{
		return 'ManyToManyContainer';
	}
}

?>