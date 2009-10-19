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
class OrmOneToManyClassCodeConstructor extends ClassCodeConstructor
{
	/**
	 * @var OrmProperty
	 */
	private $ormProperty;

	/**
	 * @return OrmOneToManyClassCodeConstructor
	 */
	static function create(OrmClass $ormClass, OrmProperty $ormProperty)
	{
		return new self ($ormClass, $ormProperty);
	}

	function __construct(OrmClass $ormClass, OrmProperty $ormProperty)
	{
		$this->ormProperty = $ormProperty;

		parent::__construct($ormClass);
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
	function getClassName()
	{
		return ucfirst($this->ormProperty->getName());
	}

	/**
	 * @return void
	 */
	protected function findMembers()
	{
		$this->classMethods[] = <<<EOT
	function __construct({$this->ormClass->getName()} \$parent)
	{
		parent::__construct(
			\$parent,
			{$this->ormClass->getEntityName()}::map()
		);
	}
EOT;

		$this->classMethods[] = <<<EOT
	/**
	 * @return OrmProperty
	 */
	function getReferentialProperty()
	{
		return {$this->ormClass->getName()}::map()->getProperty('{$this->ormProperty->getName()}');
	}
EOT;
	}

	protected function getClassType()
	{
		return 'abstract';
	}

	protected function getExtendsClassName()
	{
		return 'OneToManyContainer';
	}
}

?>