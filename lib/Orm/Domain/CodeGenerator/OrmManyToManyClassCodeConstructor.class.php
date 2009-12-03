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
 * Many-to-many worker generator
 *
 * @ingroup Orm_Domain_CodeGenerator
 */
class OrmManyToManyClassCodeConstructor extends OrmRelatedClassCodeConstruct
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
		return false;
	}

	function getClassName()
	{
		return ucfirst($this->ormProperty->getName());
	}

	protected function findMembers()
	{
		$this->classMethods[] = <<<EOT
	function __construct({$this->ormClass->getName()} \$parent)
	{
		parent::__construct(
			\$parent,
			{$this->ormClass->getName()}::orm(),
			{$this->ormClass->getName()}::orm()->getLogicalSchema()->getProperty('{$this->ormProperty->getName()}')->getType()
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