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
class OrmPhysicalSchemaClassCodeConstructor extends ClassCodeConstructor
{
	/**
	 * @return string
	 */
	function getClassName()
	{
		return $this->ormClass->getEntityName() . 'EntityPhysicalSchema';
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
		return array('IPhysicallySchematic');
	}

	/**
	 * @return string
	 */
	private function getDBFieldsArray()
	{
		$fields = array();
		foreach ($this->ormClass->getDBFields() as $field) {
			$fields[] = '\'' . $field . '\'';
		}

		return join('', array(
			'array(',
			join(', ', $fields),
			')'
		));
	}

	/**
	 * @return void
	 */
	protected function findMembers()
	{
		$this->classMethods[] = <<<EOT
	/**
	 * @see IPhysicallySchematic::getDBTableName()
	 * @return string
	 */
	function getDBTableName()
	{
		return '{$this->ormClass->getDBTableName()}';
	}
EOT;

		$this->classMethods[] = <<<EOT
	/**
	 * @see IPhysicallySchematic::getDBFields()
	 * @return array
	 */
	function getDBFields()
	{
		return {$this->getDBFieldsArray()};
	}
EOT;
	}
}

?>