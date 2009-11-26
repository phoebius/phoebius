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
class OrmAutoClassCodeConstructor extends ClassCodeConstructor
{
	function getClassName()
	{
		return 'Auto' .  $this->ormClass->getEntityName();
	}

	function isPublicEditable()
	{
		return false;
	}

	protected function getClassType()
	{
		return 'abstract';
	}

	protected function findMembers()
	{
		$ormEntityHolder = $this->ormClass->getEntityName() . 'Entity';

		$this->classMethods[] = <<<EOT
	/**
	 * @return {$ormEntityHolder}
	 */
	static function orm()
	{
		return {$ormEntityHolder}::getInstance();
	}
EOT;

		$this->classMethods[] = <<<EOT
	/**
	 * @return IOrmEntityMapper
	 */
	static function map()
	{
		return self::orm()->getMap();
	}
EOT;

		if ($this->ormClass->hasDao()) {
			$this->classMethods[] = <<<EOT
	/**
	 * @return IOrmEntityAccessor
	 */
	static function dao()
	{
		return self::orm()->getDao();
	}
EOT;

			$this->classMethods[] = <<<EOT
	/**
	 * @return EntityQuery
	 */
	static function query()
	{
		return new EntityQuery(self::orm());
	}
EOT;
		}

		foreach ($this->ormClass->getProperties() as $ormProperty) {
			$this->fetchClassMembers($ormProperty);
		}
	}

	protected function getExtendsClassName()
	{
		return
			$this->ormClass->getIdentifier()
				? 'IdentifiableOrmEntity'
				: 'OrmEntity';
	}

	protected function getImplementsInterfaceNames()
	{
		$interfaces = array('IOrmRelated');
		if ($this->ormClass->hasDao()) {
			$interfaces[] = 'IDaoRelated';
		}

		return $interfaces;
	}

	/**
	 * @return void
	 */
	private function fetchClassMembers(OrmProperty $property)
	{
		$visibility = $property->getVisibility();

		if ($visibility->is(OrmPropertyVisibility::TRANSPARENT)) {
			return;
		}

		$type = $property->getType();

		// make property itself
		$this->classProperties[] = $type->toField($this->ormClass, $property);

		// make setter
		if ($visibility->isSettable()) {
			$this->classMethods[] = $type->toSetter($this->ormClass, $property);

			if ($property->isIdentifier()) {
				$this->classMethods[] = <<<EOT
	function _setId(\$id)
	{
		\$this->{$property->getSetter()}(\$id);

		return \$this;
	}
EOT;
			}
		}

		// make getter
		if ($visibility->isGettable()) {
			$this->classMethods[] = $type->toGetter($this->ormClass, $property);

			if ($property->isIdentifier()) {
				$this->classMethods[] = <<<EOT
	function _getId()
	{
		return \$this->get{$property->getGetter()}();
	}
EOT;
			}
		}
	}
}

?>