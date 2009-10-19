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
		return array(
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
{$this->getGetPropertiesMethodBody()}
	}
EOL;

		$this->classMethods[] = <<<EOL
	/**
	 * @return IOrmProperty
	 */
	function getProperty(\$name)
	{
		if (!in_array(\$name, \$this->propertyNames)) {
			throw new ArgumentException('name', \$name);
		}

		\$properties = \$this->getProperties();

		return \$properties[\$name];
	}
EOL;
	}
}

?>