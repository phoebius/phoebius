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
class OrmPhysicalSchemaClassCodeConstructor extends OrmRelatedClassCodeConstructor
{
	function getClassName()
	{
		return $this->ormClass->getEntityName() . 'EntityPhysicalSchema';
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
		return array('IPhysicallySchematic');
	}

	protected function findMembers()
	{
		$this->classMethods[] = <<<EOT
	/**
	 * @see IPhysicallySchematic::getTable()
	 * @return string
	 */
	function getTable()
	{
		return '{$this->ormClass->getTable()}';
	}
EOT;

		$this->classMethods[] = <<<EOT
	function getFields()
	{
		return {$this->getFieldsPhpArray()};
	}
EOT;
	}

	private function getFieldsPhpArray()
	{
		$fields = array();
		foreach ($this->ormClass->getFields() as $field) {
			$fields[] = '\'' . $field . '\'';
		}

		return join('', array(
			'array(',
			join(', ', $fields),
			')'
		));
	}
}

?>