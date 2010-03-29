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
class OrmManyToManyClassCodeConstructor extends OrmRelatedClassCodeConstructor
{
	/**
	 * @var OrmProperty
	 */
	private $ormProperty;

	/**
	 * @param OrmClass $ormClass object that represents a class to be generated
	 * @param OrmProperty $ormProperty property that representa many-to-many relation
	 */
	function __construct(OrmClass $ormClass, OrmProperty $ormProperty)
	{
		$this->ormProperty = $ormProperty;

		parent::__construct($ormClass);
	}

	function isPublicEditable()
	{
		return true;
	}

	function getClassName()
	{
		return ucfirst($this->ormProperty->getName()) . 'Container';
	}

	protected function findMembers()
	{
		$type = $this->ormProperty->getType();

		$this->classMethods[] = <<<EOT
	function __construct({$this->ormClass->getName()} \$parent, \$readOnly = true)
	{
		parent::__construct(
			\$parent,
			{$type->getEncapsulant()->getEntityName()}::orm(),
			{$this->ormClass->getName()}::orm()->getLogicalSchema()->getProperty('{$this->ormProperty->getName()}'),
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