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
class OrmLogicalSchemaClassCodeConstructor extends ClassCodeConstructor
{
	/**
	 * @return string
	 */
	function getClassName()
	{
		return $this->ormClass->getEntityName() . 'EntityLogicalSchema';
	}

	/**
	 * @return boolean
	 */
	function isPublicEditable()
	{
		return false;
	}

	protected function getClassType()
	{
		return 'final';
	}

	/**
	 * @return void
	 */
	protected function getImplementsInterfaceNames()
	{
		return array('ILogicallySchematic');
	}

	/**
	 * @return string
	 */
	private function getIdentifierMethodReturn()
	{
		if (($identifier = $this->ormClass->getIdentifier())) {
			return "\$this->getProperty('{$identifier->getName()}')";
		}
		else {
			return "null";
		}
	}

	/**
	 * @return string
	 */
	private function getPropertyNameArray()
	{
		$names = array();
		foreach ($this->ormClass->getPropertyNames() as $name) {
			$names[] = '\'' . $name . '\'';
		}

		return join('', array(
			'array(',
			join(',', $names),
			')'
		));
	}

	/**
	 * @return string
	 */
	private function getGetPropertiesMethodBody()
	{
		$arrayItems = array();
		foreach ($this->ormClass->getProperties() as $property) {
			$arrayItems[] =
				"\t\t\t"
				. '\'' . $property->getName() . '\''
				. ' => '
				. $property->toPhpCall();
		}

		$arrayContents = join(",\n", $arrayItems);
		return <<<EOT
		array(
{$arrayContents}
		);
EOT;
	}

	/**
	 * @return void
	 */
	protected function findMembers()
	{
		$this->classProperties[] = <<<EOL
	private \$propertyNames = {$this->getPropertyNameArray()};
EOL;

		$this->classMethods[] = <<<EOL
	/**
	 * Returns the name of the class representing an entity
	 * @return string
	 */
	function getEntityName()
	{
		return '{$this->ormClass->getEntityName()}';
	}
EOL;

		$this->classMethods[] = <<<EOL
	/**
	 * @return OrmEntity
	 */
	function getNewEntity()
	{
		return new {$this->ormClass->getEntityName()};
	}
EOL;

		$this->classMethods[] = <<<EOL
	/**
	 * @return IOrmProperty|null
	 */
	function getIdentifier()
	{
		return {$this->getIdentifierMethodReturn()};
	}
EOL;

		$this->classMethods[] = <<<EOL
	/**
	 * Gets the set of {@link IOrmProperty}
	 * @return array
	 */
	function getProperties()
	{
{$this->getGetPropertiesMethodBody()};
	}
EOL;

		$this->classMethods[] = <<<EOL
	/**
	 * @return IOrmProperty
	 */
	function getProperty(\$name)
	{
		if (!isset(\$this->propertyNames[\$name])) {
			throw new ArgumentException('name');
		}

		\$properties = \$this->getProperties();

		return \$properties[\$name];
	}
EOL;
	}
}

?>