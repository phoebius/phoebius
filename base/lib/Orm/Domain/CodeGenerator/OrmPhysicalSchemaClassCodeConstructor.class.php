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