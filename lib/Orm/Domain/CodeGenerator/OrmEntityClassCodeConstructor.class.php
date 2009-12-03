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
 * Generates a helper class for accessing auxiliary structures of ORM-related entity
 *
 * @ingroup Orm_Domain_CodeGenerator
 */
class OrmEntityClassCodeConstructor extends OrmRelatedClassCodeConstruct
{
	function getClassName()
	{
		return $this->ormClass->getEntityName() . 'Entity';
	}

	function isPublicEditable()
	{
		return true;
	}

	protected function getExtendsClassName()
	{
		return 'Auto' . $this->getClassName();
	}

	protected function getClassType()
	{
		return 'final';
	}

	protected function findMembers()
	{
		$this->classMethods[] = <<<EOT
	/**
	 * @return {$this->getClassName()}
	 */
	static function getInstance()
	{
		return parent::instance(__CLASS__);
	}
EOT;
	}
}

?>