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
	 * @var OrmClass
	 */
	private $ormClass;

	private $classProperties = array();
	private $classMethods = array();

	/**
	 * @return OrmAutoClassCodeConstructor
	 */
	static function create(OrmClass $ormClass)
	{
		return new self ($ormClass);
	}

	function __construct(OrmClass $ormClass)
	{
		$this->ormClass = $ormClass;
	}

	/**
	 * @return string
	 */
	function getClassName()
	{
		return 'Auto' .  $this->ormClass->getName();
	}

	/**
	 * @return void
	 */
	function make(IWriteStream $writeStream)
	{
		$this->classMethods = array();
		$this->classProperties = array();

		$writeStream
			->write($this->getFileHeader())
			->write($this->getClassHeader());

		$this->prepare();
		foreach ($this->ormClass->getProperties() as $ormProperty) {
			$this->fetchClassMembers($ormProperty);
		}

		// write properties & methods here

		$writeStream
			->write(
				join(PHP_EOL . PHP_EOL, $this->classProperties)
			)
			->write(PHP_EOL)
			->write(PHP_EOL)
			->write(
				join(PHP_EOL . PHP_EOL, $this->classMethods)
			);

		$writeStream
			->write($this->getClassFooter())
			->write($this->getFileFooter());
	}

	/**
	 * @return void
	 */
	private function prepare()
	{
		$ormClassSerialized = str_replace(
			'\'',
			'\\\'',
			serialize($this->ormClass)
		);
		$this->classProperties[] = <<<EOT
	/**
	 * @var OrmClass|null
	 */
	private static \$ormClass = '{$ormClassSerialized}';
EOT;

		$this->classMethods[] = <<<EOT
	/**
	 * @return OrmClass
	 */
	static function orm()
	{
		if (is_scalar(self::\$ormClass)) {
			self::\$ormClass = unserialize(self::\$ormClass);
		}

		Assert::isTrue(
			self::\$ormClass instanceof OrmClass
		);

		return self::\$ormClass;
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
	}

	/**
	 * @return string
	 */
	private function getClassHeader()
	{
		$baseClassName =
			$this->ormClass->getIdentifier()
				? 'IdentifiableOrmEntity'
				: 'OrmEntity';

		// TODO: cut out this functionality outside to the OrmClass
		$interfaces = array('IOrmRelated');
		if ($this->ormClass->hasDao()) {
			$interfaces[] = 'IDaoRelated';
		}

		$implements = join(', ', $interfaces);

		return <<<EOT
/**
 *
 */
abstract class {$this->getClassName()} extends {$baseClassName} implements {$implements}
{

EOT;
	}

	/**
	 * @return string
	 */
	private function getClassFooter()
	{
		return <<<EOT

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
				// FIXME build a container
				Assert::notImplemented('container getters are not constructed yet');
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

			if ($this->ormClass->getIdentifier() === $ormProperty) {
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

			if ($this->ormClass->getIdentifier() === $ormProperty) {
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