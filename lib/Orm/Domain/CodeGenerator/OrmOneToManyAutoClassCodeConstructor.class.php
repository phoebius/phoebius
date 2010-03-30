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
 * One-to-many worker generator
 *
 * @ingroup Orm_Domain_CodeGenerator
 */
class OrmOneToManyAutoClassCodeConstructor extends OrmRelatedClassCodeConstructor
{
	/**
	 * @var OrmProperty
	 */
	private $ormProperty;

	/**
	 * @param OrmClass $ormClass object that represents a class to be generated
	 * @param OrmProperty $ormProperty property that representa one-to-many relation
	 */
	function __construct(OrmClass $ormClass, OrmProperty $ormProperty)
	{
		Assert::isTrue(
			$ormProperty->getType() instanceof OneToManyContainerPropertyType
		);

		$this->ormProperty = $ormProperty;

		parent::__construct($ormClass);
	}

	function isPublicEditable()
	{
		return false;
	}

	function getClassName()
	{
		return $this->ormProperty->getType()->getAutoContainerClassName($this->ormProperty);
	}

	protected function findMembers()
	{
		$type = $this->ormProperty->getType();

		$this->classMethods[] = <<<EOT
	function __construct({$this->ormClass->getName()} \$parent, \$readOnly)
	{
		parent::__construct(
			\$parent,
			{$type->getEncapsulant()->getEntityName()}Entity::getInstance(),
			{$type->getEncapsulant()->getEntityName()}Entity::getInstance()->getLogicalSchema()->getProperty('{$type->getEncapsulantProperty()->getName()}'),
			\$readOnly
		);
	}
EOT;
	}

	protected function getExtendsClassName()
	{
		return 'OneToManyContainer';
	}
}

?>