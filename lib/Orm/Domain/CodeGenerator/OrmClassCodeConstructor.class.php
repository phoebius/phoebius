<?php
/* ***********************************************************************************************
 *
 * Phoebius Framework
 *
 * **********************************************************************************************
 *
 * Copyright (c) 2009 Scand Ltd.
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
 * Generates a public class that represents ORM-related entity.
 *
 * @see OrmAutoClassCodeConstructor for aux representation of an entity
 *
 * @ingroup Orm_Domain_CodeGenerator
 */
class OrmClassCodeConstructor extends OrmRelatedClassCodeConstructor
{
	function isPublicEditable()
	{
		return true;
	}

	function getClassName()
	{
		return $this->ormClass->getEntityName();
	}

	protected function getExtendsClassName()
	{
		return 'Auto' . $this->getClassName();
	}
}

?>