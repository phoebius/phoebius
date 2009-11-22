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
	/**
	 * @return string
	 */
	function getClassName()
	{
		return 'Auto' .  $this->ormClass->getEntityName();
	}

	/**
	 * @return boolean
	 */
	function isPublicEditable()
	{
		return false;
	}

	/**
	 * @return string final|abstract|null
	 */
	protected function getClassType()
	{
		return 'abstract';
	}

	/**
	 * @return void
	 */
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
	private function buildContainerGetter(OrmProperty $ormProperty)
	{
		Assert::notImplemented(
			'container SHOULD generate code returning filled EntityQuery'
		);

		$propertyName = $ormProperty->getName();
		$capitalizedPropertyName = ucfirst($ormProperty->getName());

		// make property itself
		$this->classProperties[] = <<<EOT
	/**
	 * @var {$capitalizedPropertyName}
	 */
	private \${$propertyName};
EOT;

		$this->classMethods[] = <<<EOT
	/**
	 * @return {$capitalizedPropertyName}
	 */
	function get{$capitalizedPropertyName}()
	{
		if (!\$this->{$propertyName}) {
			\$this->{$propertyName} = new {$capitalizedPropertyName}(\$this);
		}

		return \$this->{$propertyName};
	}
EOT;
	}

	/**
	 * @return void
	 */
	private function fetchClassMembers(OrmProperty $ormProperty)
	{
		$visibility = $ormProperty->getVisibility();

		if ($visibility->is(OrmPropertyVisibility::TRANSPARENT)) {
			if ($ormProperty->getType() instanceof ContainerPropertyType) {
				$this->buildContainerGetter($ormProperty);
			}

			return;
		}

		$propertyName = $ormProperty->getName();
		$capitalizedPropertyName = ucfirst($ormProperty->getName());
		$isNullable = $ormProperty->getType()->isNullable();
		$typeImpl = $ormProperty->getType()->getImplClass();

		$actualType =
			$typeImpl
				? $typeImpl
				: 'scalar';

			if ($isNullable) {
				$actualType .= '|null';
			}

		// make property itself
		$this->classProperties[] = <<<EOT
	/**
	 * @var {$actualType}
	 */
	private \${$propertyName};
EOT;

		// make setter
		if ($visibility->isSettable()) {
			$defaulValue =
				$isNullable
					? ' = null'
					: '';

			if ($typeImpl) {
				$typeImpl .= ' ';
			}

			$this->classMethods[] = <<<EOT
	/**
	 * @param {$actualType} {$propertyName}
	 * @throws ArgumentException
	 * @return {$this->ormClass->getName()} itself
	 */
	function set{$capitalizedPropertyName}({$typeImpl}\${$propertyName}{$defaulValue})
	{
		\$this->{$propertyName} = \${$propertyName}

		return \$this;
	}
EOT;

			if (
					($identifier = $this->ormClass->getIdentifier())
					&& $identifier->getName() == $ormProperty->getName()
			) {
				$this->classMethods[] = <<<EOT
	/**
	 * @internal
	 * @return {$this->ormClass->getName()} an object itself
	 */
	function _setId(\$id)
	{
		\$this->set{$capitalizedPropertyName}(\$id);

		return \$this;
	}
EOT;
			}
		}

		// make getter
		if ($visibility->isGettable()) {
			$returnValue = $actualType . (
				$isNullable
					? '|null'
					: ''
			);

			$this->classMethods[] = <<<EOT
	/**
	 * @return {$returnValue}
	 */
	function get{$capitalizedPropertyName}()
	{
		return \$this->{$propertyName};
	}
EOT;

			if (
					($identifier = $this->ormClass->getIdentifier())
					&& $identifier->getName() == $ormProperty->getName()
			) {
				$this->classMethods[] = <<<EOT
	/**
	 * @internal
	 * @return {$returnValue}
	 */
	function _getId()
	{
		return \$this->get{$capitalizedPropertyName}();
	}
EOT;
			}
		}
	}
}

?>