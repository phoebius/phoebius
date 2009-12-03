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
 * Generates an auxiliary class that holds internal representaion of ORM-related entity
 *
 * @ingroup Orm_Domain_CodeGenerator
 */
class OrmLogicalSchemaClassCodeConstructor extends OrmRelatedClassCodeConstruct
{
	function getClassName()
	{
		return $this->ormClass->getEntityName() . 'EntityLogicalSchema';
	}

	function isPublicEditable()
	{
		return false;
	}

	protected function getClassType()
	{
		return 'final';
	}

	protected function getImplementsInterfaceNames()
	{
		return array('ILogicallySchematic');
	}

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
	 * @return OrmProperty|null
	 */
	function getIdentifier()
	{
		return {$this->getIdentifierMethodReturn()};
	}
EOL;

		$this->classMethods[] = <<<EOL
	/**
	 * Gets the set of {@link OrmProperty}
	 * @return array
	 */
	function getProperties()
	{
{$this->getGetPropertiesMethodBody()}
	}
EOL;

		$this->classMethods[] = <<<EOL
	/**
	 * @return OrmProperty
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

	private function getIdentifierMethodReturn()
	{
		if (($identifier = $this->ormClass->getIdentifier())) {
			return $identifier->toPhpCall();
		}
		else {
			return "null";
		}
	}

	private function getPropertyNameArray()
	{
		$names = array();
		foreach ($this->ormClass->getPropertyNames() as $name) {
			$names[] = '\'' . $name . '\'';
		}

		return join('', array(
			'array(',
				join(', ', $names),
			')'
		));
	}

	private function getGetPropertiesMethodBody()
	{
		$arrayItems = array();
		foreach ($this->ormClass->getProperties() as $property) {
			$arrayItems[] =
				"\t\t\t"
				. '\'' . $property->getName() . '\''
				. ' => '
				. (
					$property->isIdentifier()
						? '$this->getIdentifier()'
						: $property->toPhpCall()
				);
		}

		$arrayContents = join(",\n", $arrayItems);
		$body = <<<EOT
		return array(
{$arrayContents}
		);
EOT;

		return $body;
	}
}

?>