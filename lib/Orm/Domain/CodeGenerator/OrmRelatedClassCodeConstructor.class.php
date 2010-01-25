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
 * Represents a PHP class code generator, which build classes based on internal representation of
 * ORM-related entity
 * @ingroup Orm_Domain_CodeGenerator
 */
abstract class OrmRelatedClassCodeConstructor extends ClassCodeConstructor
{
	/**
	 * object that represents a class to be generated
	 *
	 * @var OrmClass
	 */
	protected $ormClass;

	/**
	 * @param OrmClass $ormClass object that represents a class to be generated
	 */
	function __construct(OrmClass $ormClass)
	{
		$this->ormClass = $ormClass;
	}
}

?>