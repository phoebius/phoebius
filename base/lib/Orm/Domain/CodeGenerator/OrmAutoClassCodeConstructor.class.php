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
		$isObjective = (
				class_exists($typeImpl)
				&& Type::create($typeImpl)->isDescendantOf(new Type('BuiltInType'))
			)
				? false
				: true;
		$actualType =
			$isObjective
				? $typeImpl
				: strtolower($typeImpl);

		// make property itself
		$this->classProperties[] = <<<EOT
	/**
	 * @var {$actualType}
	 */
	private \${$propertyName};
EOT;

		// make setter
		if ($visibility->isSettable()) {
			if (!$isObjective) {
				$typeHint = '/* ' . $typeImpl . ' */';

				if ($isNullable) {
					$setterBody = <<<EOT
is_null(\${$propertyName}) ? null : {$typeImpl}::cast(\${$propertyName})->getValue()
EOT;
				}
				else {
					$setterBody = <<<EOT
{$typeImpl}::cast(\${$propertyName})->getValue()
EOT;
				}
			}
			else {
				$typeHint = $typeImpl;
				$setterBody = '$' . $propertyName;
			}

			$defaulValue =
				$isNullable
					? ' = null'
					: '';

			$this->classMethods[] = <<<EOT
	/**
	 * @param {$actualType} {$propertyName}
	 * @throws ArgumentException
	 * @return {$this->ormClass->getName()} an object itself
	 */
	function set{$capitalizedPropertyName}({$typeHint} \${$propertyName}{$defaulValue})
	{
		\$this->{$propertyName} = {$setterBody};

		return \$this;
	}
EOT;

			if ($this->ormClass->getIdentifier()->getName() == $ormProperty->getName()) {
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

			if ($this->ormClass->getIdentifier()->getName() == $ormProperty->getName()) {
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